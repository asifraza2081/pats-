<?php

namespace App\Listeners;

use App\Events\PaymentVerified;
use App\Jobs\SendSmsJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyCandidateOfPayment implements ShouldQueue
{
    public function handle(PaymentVerified $event): void
    {
        $payment = $event->payment;
        $user = $payment->application->candidate->user;

        SendSmsJob::dispatch(
            $user->phone,
            "PATS: Your payment of PKR {$payment->amount} (Ref: {$payment->challan_ref}) has been verified. You can now download your invoice/slip once scheduled.",
            $user->id,
            'payment_verified'
        );
    }
}
