<?php

namespace App\Observers;

use App\Models\FinancialLedger;
use App\Models\FinancialSetting;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PaymentObserver
{
    /**
     * When a payment is marked as "paid", automatically post a revenue
     * entry to the immutable financial ledger.
     */
    public function updated(Payment $payment): void
    {
        // Only fire when status transitions TO 'paid'
        if (! $payment->wasChanged('status')) return;
        if ($payment->status->value !== 'paid') return;

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
            'created_by'  => Auth::id() ?? $payment->verified_by ?? 1,
        ]);
    }
}
