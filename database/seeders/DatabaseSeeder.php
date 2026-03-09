<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ─────────────────────────────────────────
        $roles = ['super_admin', 'admin', 'data_entry', 'candidate'];
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
        ];
        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // Super admin gets all permissions
        $superAdmin = Role::findByName('super_admin');
        $superAdmin->syncPermissions(Permission::all());

        // Admin gets most permissions (not manage users)
        $admin = Role::findByName('admin');
        $admin->syncPermissions(Permission::whereNot('name', 'manage users')->get());

        // Data entry limited
        $dataEntry = Role::findByName('data_entry');
        $dataEntry->syncPermissions(
            Permission::whereIn('name', ['verify payments', 'mark attendance', 'view reports'])->get()
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

        // ── Dummy Data for End-to-End Testing ─────────────────────
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
            'min_degree_level' => 3, // Bachelor
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
            'tcid'           => 'LHE',
            'name'           => 'Expo Center Lahore',
            'city'           => 'Lahore',
            'province'       => 'Punjab',
            'address'        => 'Johar Town, Lahore',
            'total_capacity' => 500,
            'is_active'      => true,
        ]);
        $center1->projects()->attach([$project1->id, $project2->id]);

        $center2 = \App\Models\TestCenter::create([
            'tcid'           => 'ISB',
            'name'           => 'Pak-China Friendship Center',
            'city'           => 'Islamabad',
            'province'       => 'Federal',
            'address'        => 'Garden Avenue, Shakarparian',
            'total_capacity' => 800,
            'is_active'      => true,
        ]);
        $center2->projects()->attach([$project1->id, $project2->id]);

        $batch1 = \App\Models\Batch::create([
            'project_id'    => $project1->id,
            'center_id'     => $center1->id,
            'batch_number'  => 1,
            'test_date'     => now()->addDays(15),
            'reporting_time'=> '08:00:00',
            'start_time'    => '09:00:00',
            'total_seats'   => 100,
            'booked_seats'  => 0,
            'envelope_size' => 20,
        ]);

        $batch2 = \App\Models\Batch::create([
            'project_id'    => $project2->id,
            'center_id'     => $center2->id,
            'batch_number'  => 1,
            'test_date'     => now()->addDays(20),
            'reporting_time'=> '09:00:00',
            'start_time'    => '10:00:00',
            'total_seats'   => 200,
            'booked_seats'  => 0,
            'envelope_size' => 20,
        ]);

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
                'father_name'          => 'John Doe Sr.',
                'gender'               => $i % 2 === 0 ? 'Female' : 'Male',
                'dob'                  => now()->subYears(25)->format('Y-m-d'),
                'province_of_domicile' => 'Punjab',
                'district_of_domicile' => 'Lahore',
                'postal_address'       => '123 Fake Street, Lahore',
                'profile_locked'       => true,
                'photo_path'           => null,
            ]);

            \App\Models\EducationHistory::create([
                'candidate_id'    => $candidateInfo->id,
                'degree_level'    => 3, // Bachelor
                'degree_name'     => 'BS Computer Science',
                'subject_major'   => 'Computer Science',
                'institution'     => 'PU Lahore',
                'passing_year'    => 2020,
                'marks_type'      => 'CGPA',
                'total_marks'     => 4.00,
                'obtained_marks'  => 3.50,
            ]);

            // Automatically apply them to jobs
            if ($i <= 10) {
                // Apply half to job 1
                \App\Models\Application::create([
                    'candidate_id' => $candidateInfo->id,
                    'job_id'       => $job1->id,
                    'batch_id'     => $batch1->id,
                    'status'       => 'submitted',
                ]);
                $batch1->increment('booked_seats');
            } else {
                // Apply half to job 3
                \App\Models\Application::create([
                    'candidate_id' => $candidateInfo->id,
                    'job_id'       => $job3->id,
                    'batch_id'     => $batch2->id,
                    'status'       => 'submitted',
                ]);
                $batch2->increment('booked_seats');
            }
        }

        echo "\n✅ PATS Seeder Complete with Rich Dummy Data!\n";
        echo "   Super Admin: admin@pats.test / Admin@1234 / CNIC: 0000000000001\n";
    }
}
