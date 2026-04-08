<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Project;
use App\Models\PatsJob;
use App\Models\TestCenter;
use App\Models\User;
use App\Models\Candidate;
use App\Models\EducationHistory;
use App\Models\Application;
use App\Models\Batch;
use App\Services\RollNumberService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MultiCenterTestSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('super_admin')->first();
        if (!$admin) {
            $admin = User::create([
                'first_name' => 'Admin', 'last_name' => 'User', 'email' => 'admin@pats.test',
                'password' => Hash::make(env('ADMIN_DEFAULT_PASSWORD', 'Admin@1234')), 'email_verified_at' => now(), 'cnic' => '0000000000001',
                'phone' => '03000000000'
            ]);
            $admin->assignRole('super_admin');
        }

        // 1. Create Project
        $project = Project::updateOrCreate(
            ['name' => 'Strategic Recruitment 2026'],
            [
                'org_name' => 'Strategic Services Corp',
                'status' => 'open',
                'open_date' => now()->subDays(5),
                'close_date' => now()->addDays(20),
                'created_by' => $admin->id
            ]
        );

        $job = PatsJob::updateOrCreate(
            ['project_id' => $project->id, 'job_code' => 201],
            ['title' => 'Data Scientist', 'bps_grade' => '17', 'total_seats' => 20, 'fee' => 2000, 'min_degree_level' => 3]
        );

        // 2. Setup 3 Cities & Centers
        $cities = [
            ['name' => 'Lahore', 'center' => 'Center LHR-01', 'tcid' => 'LHR101'],
            ['name' => 'Karachi', 'center' => 'Center KHI-01', 'tcid' => 'KHI202'],
            ['name' => 'Islamabad', 'center' => 'Center ISB-01', 'tcid' => 'ISB303'],
        ];

        $rollService = new RollNumberService();

        foreach ($cities as $cData) {
            $city = City::firstOrCreate(['name' => $cData['name']]);
            $center = TestCenter::updateOrCreate(
                ['tcid' => $cData['tcid']],
                [
                    'name' => $cData['center'],
                    'city_id' => $city->id,
                    'seating_capacity' => 1000,
                    'is_active' => true
                ]
            );
            
            if (!$center->projects()->where('project_id', $project->id)->exists()) {
                $center->projects()->attach($project->id);
            }

            // 3. Create 5 Candidates per City
            for ($i = 1; $i <= 5; $i++) {
                $unique = $cData['tcid'] . "_$i";
                $email = "cand_{$unique}@test.com";
                
                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'first_name' => 'Candidate', 'last_name' => $unique,
                        'password' => Hash::make(env('USER_DEFAULT_PASSWORD', 'password')), 'cnic' => '100000' . rand(1000000, 9999999),
                        'phone' => '0300' . rand(1000000, 9999999)
                    ]
                );
                $user->assignRole('candidate');

                $cand = Candidate::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'father_name' => 'Parent Name', 'gender' => 'Male',
                        'dob' => '1995-01-01', 'domicile_city_id' => $city->id, 'address_city_id' => $city->id,
                        'profile_locked' => true
                    ]
                );

                EducationHistory::updateOrCreate(
                    ['candidate_id' => $cand->id, 'degree_level' => 3],
                    [
                        'degree_name' => 'Masters', 'passing_year' => 2018, 'subject_major' => 'Science', 
                        'institution' => 'Uni', 'marks_type' => 'Marks', 'obtained_marks' => 850, 'total_marks' => 1100
                    ]
                );

                Application::updateOrCreate(
                    ['candidate_id' => $cand->id, 'project_id' => $project->id, 'job_id' => $job->id],
                    ['status' => 'fee_paid', 'desired_test_city_id' => $city->id]
                );
            }

            // 4. Create a Batch for this Center
            $batch = Batch::updateOrCreate(
                ['project_id' => $project->id, 'center_id' => $center->id],
                [
                    'test_date' => Carbon::tomorrow(),
                    'start_time' => '09:00',
                    'reporting_time' => '08:30',
                    'total_seats' => 500,
                    'batch_number' => 1
                ]
            );

            // 5. Allocate them (only if not already allocated)
            if ($batch->booked_seats == 0) {
                $rollService->allocateBatch($batch, 5);
                $rollService->markBatchReady($batch);
            }
        }

        echo "\n✅ Multi-Center Seeder Complete! Idempotent Run Successful.\n";
    }
}
