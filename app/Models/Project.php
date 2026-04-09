<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, Auditable;
    protected $fillable = [
        'name', 'org_name', 'logo_path', 'description',
        'open_date', 'close_date', 'test_date', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'open_date'  => 'date',
            'close_date' => 'date',
            'test_date'  => 'date',
            'status'     => ProjectStatus::class,
        ];
    }

    protected static function booted()
    {
        static::saved(function ($project) {
            \Illuminate\Support\Facades\Cache::forget('home_projects');
            \Illuminate\Support\Facades\Cache::forget('home_results');
            \Illuminate\Support\Facades\Cache::forget('home_stats');
            \Illuminate\Support\Facades\Cache::forget('home_announcements');
        });

        static::deleted(function ($project) {
            \Illuminate\Support\Facades\Cache::forget('home_projects');
            \Illuminate\Support\Facades\Cache::forget('home_results');
            \Illuminate\Support\Facades\Cache::forget('home_stats');
            \Illuminate\Support\Facades\Cache::forget('home_announcements');
        });
    }

    public function jobs()         { return $this->hasMany(PatsJob::class, 'project_id'); }
    public function centers()      { return $this->belongsToMany(TestCenter::class, 'project_centers', 'project_id', 'center_id')->withPivot('examiner_id'); }
    public function batches()      { return $this->hasMany(Batch::class, 'project_id'); }
    public function applications() { return $this->hasMany(Application::class, 'project_id'); }
    public function creator()      { return $this->belongsTo(User::class, 'created_by'); }

    public function isOpen(): bool { 
        if (!$this->open_date || !$this->close_date) return $this->status->value === 'open';
        return $this->status->value === 'open' && now()->between($this->open_date, $this->close_date); 
    }

    public function isRegistrationOpen(): bool { 
        return $this->status->value === 'open' && (!$this->close_date || now()->startOfDay()->lte($this->close_date)); 
    }

    public function unallocatedApplicationsCount(): int
    {
        return $this->applications()
            ->where('status', 'fee_paid')
            ->whereDoesntHave('examRollno')
            ->count();
    }
}
