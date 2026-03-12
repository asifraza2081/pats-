<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'application_id', 'roll_no', 'score', 'total_marks',
        'percentage', 'percentile', 'result_status',
        'published_at', 'uploaded_by', 'scanned_sheet_path', 'remarks',
    ];

    protected function casts(): array
    {
        return [
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
        return !is_null($this->published_at);
    }
}
