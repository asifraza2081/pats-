<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RollNumber extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'application_id', 'roll_number', 'slip_ready', 'assigned_at',
    ];

    protected function casts(): array
    {
        return [
            'slip_ready'  => 'boolean',
            'assigned_at' => 'datetime',
        ];
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
