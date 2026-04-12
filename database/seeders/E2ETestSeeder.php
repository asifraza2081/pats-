<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\EducationHistory;
use App\Models\User;
use App\Models\Project;
use App\Models\PatsJob;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class E2ETestSeeder extends Seeder
{
    public function run()
    {
        $user = User::updateOrCreate(
            ['email' => 'candidate@pats.test'],
            [
                'first_name' => 'Tester', 'last_name' => 'Zero', 'cnic' => '1234567890123',
                'phone' => '03001112223', 'nationality' => 'Pakistani',
                'password' => Hash::make('password'), 'phone_verified_at' => now(),
            ]
        );
        $user->assignRole('candidate');

        $cand = Candidate::updateOrCreate(
            ['user_id' => $user->id],
            [
                'father_name' => 'Grand Tester', 'gender' => 'Male', 'dob' => '1995-01-01',
                'domicile_city_id' => 1, 'address_city_id' => 1,
                'postal_address' => 'Automation Lab 1, PATS', 'profile_locked' => true,
            ]
        );

        EducationHistory::updateOrCreate(
            ['candidate_id' => $cand->id, 'degree_level' => 3],
            [
                'degree_name' => 'Bachelor of Hardening', 'subject_major' => 'Quality Assurance',
                'institution' => 'PATS Academics', 'passing_year' => 2018, 'marks_type' => 'CGPA',
                'obtained_marks' => 3.9, 'total_marks' => 4.0,
            ]
        );

        echo "E2E Test Candidate Setup Complete\n";
    }
}
