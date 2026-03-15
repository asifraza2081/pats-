<?php

namespace App\Events;

use App\Models\Batch;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SlipsPublished
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Batch $batch)
    {}
}
