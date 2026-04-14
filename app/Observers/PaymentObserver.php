<?php

namespace App\Observers;

use App\Models\FinancialLedger;
use App\Models\FinancialSetting;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PaymentObserver
{
    /**
     * When a payment is created as "paid", post to ledger.
     */
    public function created(Payment $payment): void
    {
        Cache::forget('admin_dashboard_stats');
        if ($payment->status->value === 'paid') {
            $this->postToLedger($payment);
        }
    }

    /**
     * When a payment is updated to "paid", post to ledger.
     */
    public function updated(Payment $payment): void
    {
        Cache::forget('admin_dashboard_stats');
        // Only fire if status was changed TO 'paid'
        if ($payment->wasChanged('status') && $payment->status->value === 'paid') {
            $this->postToLedger($payment);
        }
    }

    /**
     * When a payment is deleted, clear cache.
     */
    public function deleted(Payment $payment): void
    {
        Cache::forget('admin_dashboard_stats');
    }

    /**
     * Post the revenue entry to the immutable financial ledger.
     */
    private function postToLedger(Payment $payment): void
    {
        // Guard against duplicate ledger entries
        $alreadyPosted = FinancialLedger::where('source_type', Payment::class)
            ->where('source_id', $payment->id)
            ->exists();

        if ($alreadyPosted) return;

        $application = $payment->application()->with('job.project')->first();
        $project     = $application?->job?->project;

        $effectiveDate = $payment->deposit_date ?? now()->toDateString();
        $carbonDate    = Carbon::parse($effectiveDate);
        $fyStart       = FinancialSetting::fyStartMonth();

        FinancialLedger::create([
            'type'        => 'revenue',
            'source_type' => Payment::class,
            'source_id'   => $payment->id,
            'project_id'  => $project?->id,
            'category'    => 'Application Fee Revenue',
            'description' => sprintf(
                'Candidate fee — %s | Challan: %s | Bank: %s',
                $application?->job?->title ?? 'N/A',
                $payment->challan_ref,
                $payment->bank_name ?? 'N/A'
            ),
            'amount'      => $payment->amount,
            'tax_amount'  => 0.00,
            'net_amount'  => $payment->amount,
            'ledger_date' => $effectiveDate,
            'fiscal_year' => FinancialLedger::fiscalYearFor($carbonDate, $fyStart),
            'created_by'  => $payment->verified_by ?? Auth::id() ?? \App\Models\User::role('super_admin')->first()?->id ?? 1,
        ]);
    }
}
