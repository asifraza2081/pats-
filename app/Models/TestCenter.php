<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestCenter extends Model
{
    protected $table = 'test_centers';

    protected $fillable = [
        'tcid', 'name', 'city', 'province', 'address', 'map_url',
        'total_capacity', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'      => 'boolean',
            'total_capacity' => 'integer',
        ];
    }

    public function batches()
    {
        return $this->hasMany(Batch::class, 'center_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_centers', 'center_id', 'project_id');
    }

    /** Available seats across all batches for a given project */
    public function availableSeatsForProject(int $projectId): int
    {
        return $this->batches()
            ->where('project_id', $projectId)
            ->selectRaw('SUM(total_seats - booked_seats) as available')
            ->value('available') ?? 0;
    }
}
