<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Payment;
use App\Models\RollNumber;
use App\Services\RollNumberService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(
        private RollNumberService $rollNumbers,
        private SmsService        $sms,
    ) {}

    public function index(Request $request)
    {
        $query = Payment::with(['application.candidate.user', 'application.job.project'])
            ->orderBy('created_at', 'desc');
        if ($request->filled('status')) $query->where('status', $request->status);
        $payments = $query->paginate(20);
        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['application.candidate.user', 'application.job.project', 'application.batch.center']);
        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'bank_name'      => 'nullable|string|max:80',
            'branch_code'    => 'nullable|string|max:20',
            'transaction_id' => 'nullable|string|max:100',
            'deposit_date'   => 'required|date',
        ]);

        $payment->update([
            ...$data,
            'status'      => 'paid',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        // Update application status
        $application = $payment->application;
        $application->update(['status' => 'fee_paid']);

        // Assign roll number
        $roll = $this->rollNumbers->assign($application);

        // Notify candidate
        $candidate = $application->candidate;
        $user      = $candidate->user;
        $this->sms->send(
            $user->phone,
            "PATS: Your payment for {$application->job->title} has been verified. Roll No: {$roll->roll_number}. Slip will be available once registration closes.",
            $user->id
        );

        return back()->with('success', "Payment verified. Roll No {$roll->roll_number} assigned.");
    }
}
