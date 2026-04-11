<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use App\Enums\ApplicationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::with(['candidate.user', 'job.project', 'desiredTestCity', 'examRollno.center'])
            ->latest('applied_at')
            ->paginate(25);
            
        return view('admin.applications.index', compact('applications'));
    }

    public function show(Application $app)
    {
        $app->load([
            'candidate.user', 
            'candidate.education', 
            'candidate.experience', 
            'job.project', 
            'desiredTestCity', 
            'examRollno.center', 
            'result',
            'payment'
        ]);
        
        return view('admin.applications.show', compact('app'));
    }

    /** Manually mark a single application as paid */
    public function markPaid(Application $app)
    {
        if ($app->status === ApplicationStatus::FEE_PAID || ($app->payment && $app->payment->status === PaymentStatus::PAID)) {
            return back()->with('info', 'Application is already marked as paid.');
        }

        DB::transaction(function() use ($app) {
            $payment = $app->payment;
            if (!$payment) {
                $payment = Payment::create([
                    'application_id' => $app->id,
                    'challan_ref'    => Payment::generateRef(),
                    'amount'         => $app->job->fee,
                    'status'         => PaymentStatus::PAID,
                    'bank_name'      => 'Manual/Admin',
                    'verified_by'    => Auth::id(),
                    'verified_at'    => now(),
                    'deposit_date'   => now(),
                ]);
            } else {
                $payment->update([
                    'status'      => PaymentStatus::PAID,
                    'verified_by' => Auth::id(),
                    'verified_at' => now(),
                    'deposit_date' => $payment->deposit_date ?? now(),
                ]);
            }

            $app->update(['status' => ApplicationStatus::FEE_PAID]);
            
            \App\Models\ActivityLog::log('manual_payment', $app, [
                'payment_id' => $payment->id,
                'admin_id'   => Auth::id()
            ]);
        });

        return back()->with('success', "Candidate '{$app->candidate->user->full_name}' marked as paid manually.");
    }

    /** Bulk mark multiple applications as paid */
    public function bulkMarkPaid(Request $request)
    {
        $ids = $request->input('application_ids', []);
        if (empty($ids)) {
            return back()->with('error', 'No applications selected for bulk operation.');
        }

        $count = 0;
        DB::transaction(function() use ($ids, &$count) {
            // Find applications that ARE NOT fully paid & verified yet
            $applications = Application::whereIn('id', $ids)
                ->where(function($q) {
                    $q->where('status', '!=', ApplicationStatus::FEE_PAID)
                      ->orWhereDoesntHave('payment', function($pq) {
                          $pq->where('status', PaymentStatus::PAID);
                      });
                })
                ->with(['job', 'payment'])
                ->lockForUpdate()
                ->get();

            foreach ($applications as $app) {
                // If it's a zero-fee job, just ensure status is FEE_PAID
                if ($app->job->fee <= 0) {
                    $app->update(['status' => ApplicationStatus::FEE_PAID]);
                    $count++;
                    continue;
                }

                $payment = $app->payment;
                if (!$payment) {
                    $payment = Payment::create([
                        'application_id' => $app->id,
                        'challan_ref'    => Payment::generateRef(),
                        'amount'         => $app->job->fee,
                        'status'         => PaymentStatus::PAID,
                        'bank_name'      => 'Bulk/Admin',
                        'verified_by'    => Auth::id(),
                        'verified_at'    => now(),
                        'deposit_date'   => now(),
                    ]);
                } else {
                    if ($payment->status === PaymentStatus::PAID) continue; // Already paid

                    $payment->update([
                        'status'      => PaymentStatus::PAID,
                        'verified_by' => Auth::id(),
                        'verified_at' => now(),
                        'deposit_date' => $payment->deposit_date ?? now(),
                    ]);
                }

                $app->update(['status' => ApplicationStatus::FEE_PAID]);
                $count++;
            }
        });

        return back()->with('success', "Processed {$count} candidates successfully.");
    }
}
