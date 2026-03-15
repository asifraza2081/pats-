<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchStatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'city_id'       => $this->city_id,
            'city_name'     => $this->city_name,
            'job_id'        => $this->job_id,
            'job_title'     => $this->job_title,
            'pending_count' => (int) $this->pending_count,
        ];
    }
}
