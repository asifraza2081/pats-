<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceScan extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'batch_id', 'project_id', 'center_id', 'test_date', 'file_path', 'page_number', 'uploaded_by', 'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'test_date'   => 'date',
            'uploaded_at' => 'datetime',
        ];
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
