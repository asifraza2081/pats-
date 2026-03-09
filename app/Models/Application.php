<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'candidate_id', 'job_id', 'batch_id',
        'test_city_priority_1', 'test_city_priority_2',
        'age_relaxation_type', 'age_relaxation_years',
        'status', 'eligibility_warnings', 'applied_at',
    ];

    protected function casts(): array
    {
        return [
            'eligibility_warnings' => 'array',
            'applied_at'           => 'datetime',
        ];
    }

    public function candidate()  { return $this->belongsTo(Candidate::class); }
    public function job()        { return $this->belongsTo(PatsJob::class, 'job_id'); }
    public function batch()      { return $this->belongsTo(Batch::class); }
    public function payment()    { return $this->hasOne(Payment::class); }
    public function rollNumber() { return $this->hasOne(RollNumber::class); }
    public function result()     { return $this->hasOne(Result::class); }
}

class Payment extends Model
{
    protected $fillable = [
        'application_id', 'challan_ref', 'amount',
        'bank_name', 'branch_code', 'transaction_id',
        'deposit_date', 'verified_by', 'verified_at', 'status',
    ];

    protected function casts(): array
    {
        return [
            'deposit_date' => 'date',
            'verified_at'  => 'datetime',
            'amount'       => 'float',
        ];
    }

    public function application() { return $this->belongsTo(Application::class); }
    public function verifier()    { return $this->belongsTo(User::class, 'verified_by'); }

    public function isPaid(): bool { return $this->status === 'paid'; }

    public static function generateRef(): string
    {
        return 'PATS-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }
}

class RollNumber extends Model
{
    public $timestamps = false;

    protected $fillable = ['application_id', 'roll_number', 'slip_ready', 'assigned_at'];

    protected function casts(): array
    {
        return ['slip_ready' => 'boolean', 'assigned_at' => 'datetime'];
    }

    public function application() { return $this->belongsTo(Application::class); }
}

class AttendanceScan extends Model
{
    public $timestamps = false;

    protected $table = 'attendance_scans';
    protected $fillable = ['batch_id', 'file_path', 'page_number', 'uploaded_by', 'uploaded_at'];

    protected function casts(): array { return ['uploaded_at' => 'datetime']; }

    public function batch()    { return $this->belongsTo(Batch::class); }
    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }
}

class Result extends Model
{
    protected $fillable = [
        'application_id', 'roll_number', 'score', 'total_marks',
        'percentage', 'percentile', 'result_status', 'remarks',
        'uploaded_by', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'score'        => 'float',
            'total_marks'  => 'float',
            'percentage'   => 'float',
            'percentile'   => 'float',
            'published_at' => 'datetime',
        ];
    }

    public function application() { return $this->belongsTo(Application::class); }
    public function uploader()    { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function isPublished(): bool { return ! is_null($this->published_at); }
}
