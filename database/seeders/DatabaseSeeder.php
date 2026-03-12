<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\City;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ─────────────────────────────────────────
        $roles = ['super_admin', 'admin', 'data_entry', 'candidate', 'examiner'];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
        }

        // ── Permissions ───────────────────────────────────
        $permissions = [
            'manage users',
            'manage projects', 'manage jobs',
            'manage centers',  'manage batches',
            'verify payments', 'assign rolls',
            'upload results',  'publish results',
            'mark attendance',
            'view reports',
            'view assigned sessions',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        $superAdmin = Role::findByName('super_admin');
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::findByName('admin');
        $admin->syncPermissions(Permission::whereNot('name', 'manage users')->get());

        $dataEntry = Role::findByName('data_entry');
        $dataEntry->syncPermissions(
            Permission::whereIn('name', ['verify payments', 'mark attendance', 'view reports'])->get()
        );

        $examiner = Role::findByName('examiner');
        $examiner->syncPermissions(
            Permission::whereIn('name', ['view assigned sessions', 'mark attendance', 'view reports'])->get()
        );

        // ── Super Admin User ──────────────────────────────
        $superUser = User::updateOrCreate(
            ['email' => 'admin@pats.test'],
            [
                'first_name'        => 'Super',
                'last_name'         => 'Admin',
                'cnic'              => '0000000000001',
                'phone'             => '03000000000',
                'nationality'       => 'Pakistani',
                'password'          => Hash::make('Admin@1234'),
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
            ]
        );
        $superUser->assignRole('super_admin');
        
        // ── Examiner User ──────────────────────────────
        $examinerUser = User::updateOrCreate(
            ['email' => 'examiner@pats.test'],
            [
                'first_name'        => 'Test',
                'last_name'         => 'Examiner',
                'cnic'              => '1111111111111',
                'phone'             => '03111111111',
                'nationality'       => 'Pakistani',
                'password'          => Hash::make('password'),
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
            ]
        );
        $examinerUser->assignRole('examiner');

        // ── Cities ────────────────────────────────────────
        $cityLhe = City::create(['name' => 'Lahore', 'province' => 'Punjab', 'is_test_center' => true]);
        $cityIsb = City::create(['name' => 'Islamabad', 'province' => 'Federal', 'is_test_center' => true]);
        $cityKhi = City::create(['name' => 'Karachi', 'province' => 'Sindh', 'is_test_center' => true]);
        $cityPew = City::create(['name' => 'Peshawar', 'province' => 'KPK', 'is_test_center' => true]);
        $cityQta = City::create(['name' => 'Quetta', 'province' => 'Balochistan', 'is_test_center' => true]);

        // ── Dummy Data ─────────────────────
        $project1 = \App\Models\Project::create([
            'name'        => 'Ministry of IT Recruitment 2026',
            'org_name'    => 'Ministry of Information Technology',
            'description' => 'A large scale hiring drive for IT professionals.',
            'open_date'   => now()->subDays(5),
            'close_date'  => now()->addDays(15),
            'status'      => 'open',
            'created_by'  => $superUser->id,
        ]);

        $project2 = \App\Models\Project::create([
            'name'        => 'Federal Board of Revenue (Customs)',
            'org_name'    => 'FBR Pakistan',
            'description' => 'Recruitment for Customs Inspectors and Support Staff.',
            'open_date'   => now()->subDays(2),
            'close_date'  => now()->addDays(20),
            'status'      => 'open',
            'created_by'  => $superUser->id,
        ]);

        $job1 = \App\Models\PatsJob::create([
            'project_id'       => $project1->id,
            'job_code'         => 1,
            'title'            => 'Software Engineer',
            'department'       => 'IT Dept',
            'bps_grade'        => 'BPS-17',
            'total_seats'      => 10,
            'fee'              => 1000,
            'min_degree_level' => 3, 
            'age_min'          => 18,
            'age_max'          => 45,
        ]);

        $job2 = \App\Models\PatsJob::create([
            'project_id'       => $project1->id,
            'job_code'         => 2,
            'title'            => 'Network Administrator',
            'department'       => 'Infrastructure',
            'bps_grade'        => 'BPS-16',
            'total_seats'      => 5,
            'fee'              => 800,
            'min_degree_level' => 3,
            'age_min'          => 18,
            'age_max'          => 35,
        ]);

        $job3 = \App\Models\PatsJob::create([
            'project_id'       => $project2->id,
            'job_code'         => 1,
            'title'            => 'Customs Inspector',
            'department'       => 'Field Operations',
            'bps_grade'        => 'BPS-16',
            'total_seats'      => 50,
            'fee'              => 1200,
            'min_degree_level' => 3,
            'age_min'          => 20,
            'age_max'          => 28,
        ]);

        $center1 = \App\Models\TestCenter::create([
            'tcid'             => 'LHE',
            'name'             => 'Expo Center Lahore',
            'city_id'          => $cityLhe->id,
            'address'          => 'Johar Town, Lahore',
            'seating_capacity' => 500,
            'is_active'        => true,
        ]);
        $center1->projects()->attach([$project1->id, $project2->id]);

        $center2 = \App\Models\TestCenter::create([
            'tcid'             => 'ISB',
            'name'             => 'Pak-China Friendship Center',
            'city_id'          => $cityIsb->id,
            'address'          => 'Garden Avenue, Shakarparian',
            'seating_capacity' => 800,
            'is_active'        => true,
        ]);
        $center2->projects()->attach([$project1->id, $project2->id]);

        // Create 20 Dummy Candidates
        for ($i = 1; $i <= 20; $i++) {
            $user = User::create([
                'first_name'        => 'Candidate',
                'last_name'         => 'Test ' . $i,
                'cnic'              => '35202' . str_pad($i, 7, '0', STR_PAD_LEFT) . '1',
                'phone'             => '0300' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'email'             => "candidate{$i}@pats.test",
                'nationality'       => 'Pakistani',
                'password'          => Hash::make('password'),
                'phone_verified_at' => now(),
            ]);
            $user->assignRole('candidate');

            $candidateInfo = \App\Models\Candidate::create([
                'user_id'              => $user->id,
                'father_name'          => 'Father of Candidate ' . $i,
                'gender'               => $i % 2 === 0 ? 'Female' : 'Male',
                'dob'                  => now()->subYears(20 + ($i % 10))->format('Y-m-d'),
                'domicile_city_id'     => ($i % 3 === 0) ? $cityKhi->id : (($i % 2 === 0) ? $cityLhe->id : $cityIsb->id),
                'address_city_id'      => $cityLhe->id,
                'postal_address'       => $i . ' Main Blvd, Sector ' . chr(65 + ($i % 5)),
                'profile_locked'       => $i <= 15, // Most are locked
                'photo_path'           => null,
                'religion'             => 'Islam',
                'province_of_domicile' => 'Punjab',
                'district_of_domicile' => 'Lahore',
            ]);

            // Education
            \App\Models\EducationHistory::create([
                'candidate_id'    => $candidateInfo->id,
                'degree_level'    => 3, 
                'degree_name'     => ($i % 2 === 0) ? 'BS Computer Science' : 'BS Electrical Engineering',
                'subject_major'   => ($i % 2 === 0) ? 'Computer Science' : 'Engineering',
                'institution'     => 'University of ' . (($i % 2 === 0) ? 'Lahore' : 'Islamabad'),
                'passing_year'    => 2018 + ($i % 5),
                'marks_type'      => ($i % 3 === 0) ? 'CGPA' : 'Marks',
                'obtained_marks'  => ($i % 3 === 0) ? 3.50 : 850,
                'total_marks'     => ($i % 3 === 0) ? 4.00 : 1100,
            ]);

            // Work Experience (for even candidates)
            if ($i % 2 === 0) {
                \App\Models\WorkExperience::create([
                    'candidate_id'      => $candidateInfo->id,
                    'organization_name' => 'Tech Corp ' . $i,
                    'designation'       => 'Junior Developer',
                    'job_type'          => 'Private',
                    'from_date'         => now()->subYears(2),
                    'is_current'        => true,
                ]);
            }

            // Varied Applications
            if ($i <= 8) {
                \App\Models\Application::create([
                    'candidate_id'         => $candidateInfo->id,
                    'job_id'               => $job1->id,
                    'project_id'           => $project1->id,
                    'desired_test_city_id' => $cityLhe->id,
                    'status'               => 'fee_paid',
                    'applied_at'           => now()->subDays($i),
                ]);
            } elseif ($i <= 14) {
                \App\Models\Application::create([
                    'candidate_id'         => $candidateInfo->id,
                    'job_id'               => $job3->id,
                    'project_id'           => $project2->id,
                    'desired_test_city_id' => $cityIsb->id,
                    'status'               => 'submitted',
                    'applied_at'           => now()->subHours($i),
                ]);
            }
            // Candidates 15-20 have NO applications (Testing Archive view)
        }

        echo "\n✅ PATS Phase 2 Seeder Complete with City Architecture!\n";
        echo "   Super Admin: admin@pats.test / Admin@1234 / CNIC: 0000000000001\n";
    }
}
