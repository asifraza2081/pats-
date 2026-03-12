<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $table = 'candidates';

    protected $fillable = [
        'user_id', 'father_name', 'dob', 'gender', 'marital_status', 'religion',
        'blood_group', 'current_occupation', 'disability', 'disability_type',
        'domicile_city_id', 'province_of_domicile', 'district_of_domicile',
        'address_city_id', 'permanent_address',
        'postal_address', 'same_postal_address', 'alternate_phone',
        'photo_path', 'cnic_front_path', 'profile_locked',
    ];

    protected function casts(): array
    {
        return [
            'dob'                 => 'date',
            'disability'          => 'boolean',
            'same_postal_address' => 'boolean',
            'profile_locked'      => 'boolean',
        ];
    }

    // ── Relationships ───────────────────────────────────────
    public function user()        { return $this->belongsTo(User::class); }
    public function education()   { return $this->hasMany(EducationHistory::class); }
    public function experience()  { return $this->hasMany(WorkExperience::class); }
    public function applications(){ return $this->hasMany(Application::class); }
    public function domicileCity(){ return $this->belongsTo(City::class, 'domicile_city_id'); }
    public function addressCity() { return $this->belongsTo(City::class, 'address_city_id'); }

    // ── Helpers ─────────────────────────────────────────────

    /** Age in full years as of today */
    public function getAgeAttribute(): int
    {
        return $this->dob ? (int) $this->dob->diffInYears(now()) : 0;
    }

    /** Highest education degree_level */
    public function getMaxDegreeLevelAttribute(): int
    {
        return (int) $this->education()->max('degree_level');
    }

    /**
     * Total years of work experience.
     * Optionally filtered by sector ('Public'|'Private').
     */
    public function totalExperienceYears(?string $sector = null): float
    {
        $query = $this->experience();
        if ($sector && $sector !== 'Any') {
            $query->where('job_type', $sector);
        }
        $total = 0.0;
        foreach ($query->get() as $exp) {
            $end    = $exp->is_current ? now() : Carbon::parse($exp->to_date);
            $total += Carbon::parse($exp->from_date)->floatDiffInYears($end);
        }
        return round($total, 2);
    }

    /**
     * Profile completion percentage (0–100).
     * Required fields for 100%:
     * father_name, dob, gender, marital_status, religion,
     * domicile_city_id, address_city_id,
     * permanent_address, postal_address,
     * photo_path, cnic_front_path,
     * ≥1 education entry
     */
    public function completionPercent(): int
    {
        $fields = [
            'father_name', 'dob', 'gender', 'marital_status', 'religion',
            'domicile_city_id', 'address_city_id',
            'permanent_address', 'postal_address',
            'photo_path', 'cnic_front_path',
        ];
        $filled = 0;
        $total  = count($fields) + 1; // +1 for education only
        foreach ($fields as $f) {
            if ($f === 'postal_address' && $this->same_postal_address) {
                if (!empty($this->permanent_address)) {
                    $filled++;
                }
                continue;
            }
            if (!empty($this->$f)) $filled++;
        }
        if ($this->education()->exists())  $filled++;
        return (int) round(($filled / $total) * 100);
    }
}
