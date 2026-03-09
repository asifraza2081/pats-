<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'application_id', 'roll_number', 'score', 'total_marks',
        'percentage', 'percentile', 'result_status', 'is_published',
        'published_at', 'published_by',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'score'        => 'decimal:2',
            'total_marks'  => 'decimal:2',
            'percentage'   => 'decimal:2',
            'percentile'   => 'decimal:2',
        ];
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function isPublished(): bool
    {
        return $this->is_published === true;
    }
}
