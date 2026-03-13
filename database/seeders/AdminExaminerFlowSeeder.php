<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\PatsJob;
use App\Models\TestCenter;
use App\Models\City;
use App\Models\Candidate;
use App\Models\Application;
use App\Models\Batch;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminExaminerFlowSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles & Permissions ──────────────────────────
        $roles = ['super_admin', 'admin', 'data_entry', 'candidate', 'examiner'];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
        }

        $permissions = [
            'manage users', 'manage projects', 'manage jobs',
            'manage centers', 'manage batches', 'verify payments',
            'assign rolls', 'upload results', 'publish results',
            'mark attendance', 'view reports', 'view assigned sessions',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        Role::findByName('super_admin')->syncPermissions(Permission::all());
        Role::findByName('examiner')->syncPermissions(
            Permission::whereIn('name', ['view assigned sessions', 'mark attendance', 'view reports'])->get()
        );

        // 0. Cleanup existing test data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::where('email', 'like', 'examiner.%@pats.test')
            ->orWhere('email', 'like', 'candidate.flow%@pats.test')
            ->orWhere('cnic', 'like', '99999%')
            ->orWhere('cnic', 'like', '88888%')
            ->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 0.5. Create Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@pats.test'],
            [
                'first_name' => 'System',
                'last_name' => 'Admin',
                'password' => Hash::make('Admin@1234'),
                'cnic' => '0000000000001',
                'phone' => '03000000000',
                'nationality' => 'Pakistani',
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('super_admin');

        // 1. Create Projects
        $project = Project::create([
            'name' => 'WAPDA Recruitment Drive 2026',
            'org_name' => 'WAPDA',
            'description' => 'Recruitment for various technical and non-technical positions.',
            'status' => 'open',
            'open_date' => now()->subDays(10),
            'close_date' => now()->addDays(20),
            'created_by' => $admin->id,
        ]);

        // 2. Create Jobs
        $jobs = [];
        $jobTitles = ['Assistant Manager (IT)', 'Junior Engineer', 'Line Superintendent'];
        foreach ($jobTitles as $index => $title) {
            $jobs[] = PatsJob::create([
                'project_id' => $project->id,
                'job_code' => $index + 100,
                'title' => $title,
                'department' => 'Operations',
                'bps_grade' => 'BPS-17',
                'total_seats' => 20,
                'fee' => 850,
                'min_degree_level' => 3,
                'age_min' => 21,
                'age_max' => 30,
            ]);
        }

        // 3. Create Cities and Centers
        $cityLhe = City::updateOrCreate(['name' => 'Lahore'], ['province' => 'Punjab', 'is_test_center' => true]);
        $cityIsb = City::updateOrCreate(['name' => 'Islamabad'], ['province' => 'Federal', 'is_test_center' => true]);
        $cities = [$cityLhe, $cityIsb];

        $centers = [];
        $centerData = [
            ['tcid' => '7001', 'name' => 'PATS Test Center Lahore', 'city' => $cityLhe],
            ['tcid' => '7002', 'name' => 'PATS Test Center Islamabad', 'city' => $cityIsb],
        ];

        foreach ($centerData as $data) {
            $centers[] = TestCenter::updateOrCreate(
                ['tcid' => $data['tcid']],
                [
                    'name' => $data['name'],
                    'city_id' => $data['city']->id,
                    'seating_capacity' => 200,
                    'is_active' => true,
                ]
            );
        }

        // 4. Create Examiners and Assign to Centers
        foreach ($centers as $index => $center) {
            $cityName = $cities[$index]->name;
            $email = 'examiner.' . strtolower($cityName) . '@pats.test';
            $cnic = '99999' . str_pad($index, 8, '0', STR_PAD_LEFT);

            $examiner = User::updateOrCreate(
                ['email' => $email],
                [
                    'first_name' => 'Examiner',
                    'last_name' => $cityName,
                    'password' => Hash::make('password'),
                    'cnic' => $cnic,
                    'phone' => '0399' . str_pad($index, 7, '0', STR_PAD_LEFT),
                    'nationality' => 'Pakistani',
                    'phone_verified_at' => now(),
                    'email_verified_at' => now(),
                ]
            );
            $examiner->assignRole('examiner');

            // Attach to project and assign examiner_id (or update if exists)
            DB::table('project_centers')
                ->updateOrInsert(
                    ['project_id' => $project->id, 'center_id' => $center->id],
                    ['examiner_id' => $examiner->id]
                );
        }

        // 5. Create Candidates and Applications
        for ($i = 0; $i < 50; $i++) {
            $email = 'candidate.flow' . $i . '@pats.test';
            $cnic = '88888' . str_pad($i, 8, '0', STR_PAD_LEFT);
            
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'first_name' => 'Candidate',
                    'last_name' => 'FlowTest ' . $i,
                    'password' => Hash::make('password'),
                    'cnic' => $cnic,
                    'phone' => '0388' . str_pad($i, 7, '0', STR_PAD_LEFT),
                    'nationality' => 'Pakistani',
                    'phone_verified_at' => now(),
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('candidate');

            $candidate = Candidate::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'father_name' => 'Father Name',
                    'gender' => $i % 2 == 0 ? 'Male' : 'Female',
                    'dob' => '1995-01-01',
                    'domicile_city_id' => $cities[0]->id,
                    'address_city_id' => $cities[0]->id,
                    'postal_address' => 'Test Address',
                    'profile_locked' => true,
                ]
            );

            $randomJob = $jobs[array_rand($jobs)];
            $randomCity = $cities[array_rand($cities)];

            Application::updateOrCreate(
                ['candidate_id' => $candidate->id, 'job_id' => $randomJob->id, 'project_id' => $project->id],
                [
                    'desired_test_city_id' => $randomCity->id,
                    'status' => 'fee_paid',
                    'applied_at' => now(),
                ]
            );
        }

        // 6. Create Batches for assigned centers
        foreach ($centers as $i => $center) {
            Batch::updateOrCreate(
                ['project_id' => $project->id, 'center_id' => $center->id, 'test_date' => now()->addDays(5)->toDateString(), 'batch_number' => 1],
                [
                    'reporting_time' => '08:30:00',
                    'start_time' => '09:00:00',
                    'total_seats' => 100,
                    'is_ready' => true,
                ]
            );
        }

        echo "\n✅ Admin & Examiner Flow Seeder Complete!\n";
        echo "   Examiner Lahore: examiner.lahore@pats.test / password\n";
        echo "   Examiner Islamabad: examiner.islamabad@pats.test / password\n";
    }
}
