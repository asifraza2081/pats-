<?php

namespace App\Providers;

use App\Events\PaymentVerified;
use App\Events\ResultsPublished;
use App\Events\SlipsPublished;
use App\Listeners\NotifyBatchOfResults;
use App\Listeners\NotifyBatchOfSlips;
use App\Listeners\NotifyCandidateOfPayment;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        PaymentVerified::class => [
            NotifyCandidateOfPayment::class,
        ],
        ResultsPublished::class => [
            NotifyBatchOfResults::class,
        ],
        SlipsPublished::class => [
            NotifyBatchOfSlips::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
