<?php

namespace App\Http\Controllers\Admin;

use App\Events\PaymentVerified;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct() {}

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
        $payment->load(['application.candidate.user', 'application.job.project']);
        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment)
    {
        if ($payment->status->value === 'paid') {
            return back()->with('error', 'This payment is already verified.');
        }

        $data = $request->validate([
            'bank_name'      => 'nullable|string|max:80',
            'branch_code'    => 'nullable|string|max:20',
            'transaction_id' => 'nullable|string|max:100',
            'deposit_date'   => 'required|date_format:Y-m-d',
        ]);

        $data['deposit_date'] = Carbon::parse($data['deposit_date'])->toDateString();

        $project = $payment->application->job->project;
        $boundary = $project->close_date
            ? Carbon::parse($project->close_date)->copy()->addDays(3)
            : Carbon::now()->addDays(3);

        if (Carbon::parse($data['deposit_date'])->gt($boundary)) {
            $closedOn = $project->close_date ? Carbon::parse($project->close_date)->toDateString() : '—';
            $graceEnd = $boundary->toDateString();
            return back()->with('error', "Payment rejected: The deposit date exceeds the grace period (Project closed on {$closedOn}, grace period ended on {$graceEnd}).");
        }

        $payment->update([
            ...$data,
            'status'      => 'paid',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        // Update application status (Guard against restoring progress status)
        $application = $payment->application;
        if ($application->status->value === 'submitted') {
            $application->update(['status' => 'fee_paid']);
        }

        // Notify candidate
        PaymentVerified::dispatch($payment);

        return back()->with('success', "Payment verified and receipt confirmed.");
    }
}
