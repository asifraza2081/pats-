<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\City;
use App\Models\Project;
use App\Models\PatsJob;
use App\Models\TestCenter;
use App\Models\Candidate;
use App\Models\Application;
use App\Models\EducationHistory;
use App\Models\WorkExperience;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class SystemStagingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions Base
        $this->call(DatabaseSeeder::class);

        // 2. Geography Expansion
        $provinces = [
            'Punjab' => ['Lahore', 'Faisalabad', 'Multan', 'Rawalpindi'],
            'Sindh' => ['Karachi', 'Hyderabad', 'Sukkur'],
            'KPK' => ['Peshawar', 'Abbottabad', 'Swat'],
            'Balochistan' => ['Quetta', 'Gwadar'],
            'Federal' => ['Islamabad']
        ];

        $cities = [];
        foreach ($provinces as $province => $cityNames) {
            foreach ($cityNames as $cityName) {
                $cities[] = City::firstOrCreate(['name' => $cityName], ['province' => $province, 'is_test_center' => true]);
            }
        }

        // 3. Infrastructure: Test Centers & Examiners
        $centers = [];
        $examiners = [];
        foreach ($cities as $index => $city) {
            $center = TestCenter::create([
                'tcid' => str_pad($index + 8000, 4, '0', STR_PAD_LEFT),
                'name' => "PATS Excellence Center " . $city->name,
                'city_id' => $city->id,
                'address' => "Main Street, " . $city->name,
                'seating_capacity' => rand(500, 2000),
                'is_active' => true,
            ]);
            $centers[] = $center;

            // Create Examiner for this city/center
            $examinerUser = User::create([
                'first_name' => 'Examiner',
                'last_name' => $city->name,
                'email' => "examiner." . strtolower(str_replace(' ', '', $city->name)) . "@pats.test",
                'cnic' => '77777' . str_pad($index, 8, '0', STR_PAD_LEFT),
                'phone' => '0377' . str_pad($index, 7, '0', STR_PAD_LEFT),
                'password' => Hash::make(env('USER_DEFAULT_PASSWORD', 'password')),
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
            ]);
            $examinerUser->assignRole('examiner');
            $examiners[$center->id] = $examinerUser;
        }

        // 4. Recruitment Projects
        $projectsData = [
            ['name' => 'National Health Program 2026', 'org' => 'Ministry of Health', 'type' => 'Medical'],
            ['name' => 'KPK Education Recruitment', 'org' => 'Secondary Education Dept', 'type' => 'Educational'],
            ['name' => 'Punjab Power & Tech Drive', 'org' => 'Energy Department', 'type' => 'Technical'],
            ['name' => 'Sindh Revenue Authority Board', 'org' => 'Sindh Revenue Board', 'type' => 'Administrative'],
            ['name' => 'National Security Guard Force', 'org' => 'Home Department', 'type' => 'Security'],
        ];

        $projects = [];
        $admin = User::role('super_admin')->first();

        foreach ($projectsData as $index => $data) {
            $projects[] = Project::create([
                'name' => $data['name'],
                'org_name' => $data['org'],
                'description' => "Large scale recruitment drive for " . $data['type'] . " professionals across Pakistan.",
                'status' => 'open',
                'open_date' => now()->subDays(15),
                'close_date' => now()->addDays(30),
                'created_by' => $admin->id,
            ]);
        }

        // Create Jobs for each project
        $jobs = [];
        foreach ($projects as $project) {
            $jobConfigs = [
                ['title' => 'Senior Officer', 'grade' => 'BPS-17', 'fee' => 1200, 'min_deg' => 4, 'age' => [25, 45]], // Master+
                ['title' => 'Junior Associate', 'grade' => 'BPS-14', 'fee' => 850, 'min_deg' => 3, 'age' => [21, 35]],  // Bachelor
                ['title' => 'Technical Supervisor', 'grade' => 'BPS-16', 'fee' => 1000, 'min_deg' => 3, 'age' => [23, 40]],
            ];

            foreach ($jobConfigs as $c) {
                $jobs[] = PatsJob::create([
                    'project_id' => $project->id,
                    'job_code' => rand(10, 99),
                    'title' => $c['title'] . " — " . $project->name,
                    'department' => 'Operations',
                    'bps_grade' => $c['grade'],
                    'total_seats' => rand(50, 200),
                    'fee' => $c['fee'],
                    'min_degree_level' => $c['min_deg'],
                    'age_min' => $c['age'][0],
                    'age_max' => $c['age'][1],
                ]);
            }

            // Sync all centers to all projects for broad availability
            $project->centers()->attach(array_column($centers, 'id'));
        }

        // 5. Candidate Generation (Bulk Insert Strategy)
        $this->command->info("Seeding 500 Candidates and Applications...");
        
        $proBar = $this->command->getOutput()->createProgressBar(500);

        for ($i = 0; $i < 500; $i++) {
            $user = User::create([
                'first_name' => 'Candidate',
                'last_name' => '#' . ($i + 1),
                'email' => "candidate." . ($i + 1) . "@example.com",
                'cnic' => '55555' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'phone' => '0300' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'password' => Hash::make(env('USER_DEFAULT_PASSWORD', 'password')),
                'nationality' => 'Pakistani',
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
            ]);
            $user->assignRole('candidate');

            $candidate = Candidate::create([
                'user_id' => $user->id,
                'father_name' => "Father of " . ($i + 1),
                'gender' => rand(0, 1) ? 'Male' : 'Female',
                'dob' => Carbon::now()->subYears(rand(22, 40))->format('Y-m-d'),
                'domicile_city_id' => $cities[array_rand($cities)]->id,
                'address_city_id' => $cities[array_rand($cities)]->id,
                'postal_address' => "Address of candidate " . ($i + 1),
                'profile_locked' => true,
            ]);

            EducationHistory::create([
                'candidate_id' => $candidate->id,
                'degree_level' => 4,
                'degree_name' => 'Master of Science',
                'subject_major' => 'General Studies',
                'institution' => 'University of Pakistan',
                'passing_year' => 2021,
                'marks_type' => 'CGPA',
                'obtained_marks' => 3.5,
                'total_marks' => 4.0,
            ]);

            // Apply to 2 random jobs
            $chosenJobs = array_rand($jobs, 2);
            foreach ($chosenJobs as $jIdx) {
                $job = $jobs[$jIdx];
                Application::create([
                    'candidate_id' => $candidate->id,
                    'project_id' => $job->project_id,
                    'job_id' => $job->id,
                    'desired_test_city_id' => $candidate->domicile_city_id,
                    'status' => 'fee_paid',
                    'applied_at' => now()->subDays(rand(1, 10)),
                ]);
            }
            $proBar->advance();
        }
        $proBar->finish();

        echo "\n✅ System Staging Seeder Complete!\n";
        echo "   Candidates generated: 500\n";
        echo "   Applications generated: 1000\n";
        echo "   Projects: 5 active recruitment drives.\n";
        echo "   Admin Login: admin@pats.test / Admin@1234\n";
    }
}
