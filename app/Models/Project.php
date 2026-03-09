<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
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
        ];
    }

    public function jobs()    { return $this->hasMany(PatsJob::class, 'project_id'); }
    public function centers() { return $this->belongsToMany(TestCenter::class, 'project_centers', 'project_id', 'center_id'); }
    public function batches() { return $this->hasMany(Batch::class, 'project_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function isOpen(): bool       { return $this->status === 'open' && now()->between($this->open_date, $this->close_date); }
    public function isRegistrationOpen(): bool { return $this->isOpen() && now()->lte($this->close_date); }
}
