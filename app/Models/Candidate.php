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

    /** 
     * Age in full years.
     * Defaults to Project Close Date if available, else today.
     */
    public function age(?Carbon $asOfDate = null): int
    {
        $refDate = $asOfDate ?? now();
        return $this->dob ? (int) $this->dob->diffInYears($refDate) : 0;
    }

    /** Legacy accessor for existing blade views, uses today */
    public function getAgeAttribute(): int
    {
        return $this->age(now());
    }

    /** 
     * Highest education degree_level.
     * Defaults to Project Close Year if available, else current year.
     */
    public function maxDegreeLevel(?int $asOfYear = null): int
    {
        $refYear = $asOfYear ?? now()->year;
        return (int) $this->education()
            ->where('passing_year', '<=', $refYear)
            ->max('degree_level');
    }

    /** Legacy accessor for existing blade views, uses current year */
    public function getMaxDegreeLevelAttribute(): int
    {
        return $this->maxDegreeLevel(now()->year);
    }

    /**
     * Total years of work experience.
     * Optionally filtered by sector ('Public'|'Private').
     */
    public function totalExperienceYears(?string $sector = null, ?Carbon $asOfDate = null): float
    {
        $refDate = $asOfDate ?? now();
        $query = $this->experience();
        if ($sector && $sector !== 'Any') {
            $query->where('job_type', $sector);
        }

        $intervals = $query->get()->map(function($exp) use ($refDate) {
            return [
                'from' => Carbon::parse($exp->from_date),
                'to'   => $exp->is_current ? $refDate : Carbon::parse($exp->to_date)
            ];
        })->sortBy('from')->values()->toArray();

        if (empty($intervals)) return 0.0;

        // Merge overlapping intervals
        $merged = [];
        $current = $intervals[0];

        for ($i = 1; $i < count($intervals); $i++) {
            $next = $intervals[$i];
            if ($next['from']->lte($current['to'])) {
                // Overlap: extend existing interval
                if ($next['to']->gt($current['to'])) {
                    $current['to'] = $next['to'];
                }
            } else {
                // No overlap: push current and move to next
                $merged[] = $current;
                $current = $next;
            }
        }
        $merged[] = $current;

        $total = 0.0;
        foreach ($merged as $interval) {
            $total += $interval['from']->floatDiffInYears($interval['to']);
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
        $total  = count($fields) + 1; // +1 for education
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
