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
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('─── PATS UNIFIED SEEDER STARTING ───');

        // 1. ROLES & PERMISSIONS
        $this->seedRolesPermissions();

        // 2. CORE SYSTEM USERS
        $admin = $this->seedCoreUsers();

        // 3. GEOGRAPHY (Major Cities of Pakistan)
        $cities = $this->seedGeography();

        // 4. INFRASTRUCTURE (Centers & Examiners)
        $infrastructure = $this->seedInfrastructure($cities);

        // 5. DEMO PROJECTS & JOBS (WAPDA Theme)
        $projectData = $this->seedProjects($admin, $infrastructure['centers']);

        // 6. CANDIDATES & APPLICATIONS (Bulk Generation)
        $this->seedCandidates($cities, $projectData);

        $this->command->info('─── SEEDING COMPLETE! ───');
        $this->command->table(['User Type', 'Email', 'Password'], [
            ['Super Admin', 'admin@pats.test', env('ADMIN_DEFAULT_PASSWORD', 'Admin@1234')],
            ['Data Entry', 'data@pats.test', env('USER_DEFAULT_PASSWORD', 'password')],
            ['Examiner (LHR)', 'examiner.lahore@pats.test', env('USER_DEFAULT_PASSWORD', 'password')],
            ['Test Candidate', 'candidate@pats.test', env('USER_DEFAULT_PASSWORD', 'password')],
        ]);
    }

    private function seedRolesPermissions()
    {
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
        Role::findByName('admin')->syncPermissions(Permission::whereNot('name', 'manage users')->get());
        Role::findByName('data_entry')->syncPermissions(
            Permission::whereIn('name', ['verify payments', 'mark attendance', 'view reports'])->get()
        );
        Role::findByName('examiner')->syncPermissions(
            Permission::whereIn('name', ['view assigned sessions', 'mark attendance', 'view reports'])->get()
        );
    }

    private function seedCoreUsers()
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@pats.test'],
            [
                'first_name' => 'System', 'last_name' => 'Admin', 'cnic' => '0000000000001',
                'phone' => '03000000000', 'nationality' => 'Pakistani',
                'password' => Hash::make(env('ADMIN_DEFAULT_PASSWORD', 'Admin@1234')), 'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('super_admin');

        $dataEntry = User::updateOrCreate(
            ['email' => 'data@pats.test'],
            [
                'first_name' => 'Data', 'last_name' => 'Entry', 'cnic' => '0000000000002',
                'phone' => '03000000001', 'nationality' => 'Pakistani',
                'password' => Hash::make(env('USER_DEFAULT_PASSWORD', 'password')), 'email_verified_at' => now(),
            ]
        );
        $dataEntry->assignRole('data_entry');

        return $admin;
    }

    private function seedGeography()
    {
        $provinces = [
            'Punjab' => ['Lahore', 'Faisalabad', 'Multan', 'Rawalpindi'],
            'Sindh' => ['Karachi', 'Hyderabad', 'Sukkur'],
            'KPK' => ['Peshawar', 'Abbottabad'],
            'Balochistan' => ['Quetta'],
            'Federal' => ['Islamabad']
        ];

        $cities = [];
        foreach ($provinces as $province => $cityNames) {
            foreach ($cityNames as $cityName) {
                $cities[] = City::updateOrCreate(['name' => $cityName], ['province' => $province, 'is_test_center' => true]);
            }
        }
        return $cities;
    }

    private function seedInfrastructure($cities)
    {
        $centers = [];
        foreach ($cities as $index => $city) {
            $center = TestCenter::updateOrCreate(
                ['tcid' => str_pad($index + 7000, 4, '0', STR_PAD_LEFT)],
                [
                    'name' => "PATS Excellence Center - " . $city->name,
                    'city_id' => $city->id,
                    'address' => "Main Street, " . $city->name,
                    'seating_capacity' => 1000,
                    'is_active' => true,
                ]
            );
            $centers[] = $center;

            // Create Examiner for this city/center
            $examEmail = 'examiner.' . strtolower(str_replace(' ', '', $city->name)) . '@pats.test';
            $examiner = User::updateOrCreate(
                ['email' => $examEmail],
                [
                    'first_name' => 'Examiner', 'last_name' => $city->name,
                    'cnic' => '99999' . str_pad($index, 8, '0', STR_PAD_LEFT),
                    'phone' => '0399' . str_pad($index, 7, '0', STR_PAD_LEFT),
                    'password' => Hash::make(env('USER_DEFAULT_PASSWORD', 'password')), 'email_verified_at' => now(),
                ]
            );
            $examiner->assignRole('examiner');
        }
        return ['centers' => $centers];
    }

    private function seedProjects($admin, $centers)
    {
        $project = Project::updateOrCreate(
            ['name' => 'WAPDA Mega Recruitment 2026'],
            [
                'org_name' => 'WAPDA',
                'description' => 'National scale recruitment for technical and non-technical staff.',
                'status' => 'open',
                'open_date' => now()->subDays(10),
                'close_date' => now()->addDays(20),
                'created_by' => $admin->id,
            ]
        );

        // Sync Centers
        $project->centers()->sync(array_column($centers, 'id'));

        $jobs = [];
        $jobTitles = [
            ['title' => 'Assistant Manager (IT)', 'code' => 101, 'fee' => 1200, 'deg' => 3],
            ['title' => 'Junior Engineer', 'code' => 102, 'fee' => 1500, 'deg' => 3],
            ['title' => 'Lines Superintendent', 'code' => 103, 'fee' => 850, 'deg' => 2],
        ];

        foreach ($jobTitles as $j) {
            $jobs[] = PatsJob::updateOrCreate(
                ['project_id' => $project->id, 'job_code' => $j['code']],
                [
                    'title' => $j['title'], 'department' => 'Operations', 'bps_grade' => 'BPS-17',
                    'total_seats' => 50, 'fee' => $j['fee'], 'min_degree_level' => $j['deg'],
                    'age_min' => 21, 'age_max' => 35,
                ]
            );
        }

        return ['project' => $project, 'jobs' => $jobs];
    }

    private function seedCandidates($cities, $projectData)
    {
        // 1. Standard Test Candidate (matched role)
        $testCandUser = User::updateOrCreate(
            ['email' => 'candidate@pats.test'],
            [
                'first_name' => 'Jane', 'last_name' => 'Doe', 'cnic' => '3520200000001',
                'phone' => '03001234567', 'nationality' => 'Pakistani',
                'password' => Hash::make(env('USER_DEFAULT_PASSWORD', 'password')), 'phone_verified_at' => now(),
            ]
        );
        $testCandUser->assignRole('candidate');

        $candidateInfo = Candidate::updateOrCreate(
            ['user_id' => $testCandUser->id],
            [
                'father_name' => 'John Doe Sr', 'gender' => 'Female', 'dob' => '1998-05-15',
                'domicile_city_id' => $cities[0]->id, 'address_city_id' => $cities[0]->id,
                'postal_address' => 'House 123, Street 4, Lahore', 'profile_locked' => false,
            ]
        );

        EducationHistory::updateOrCreate(
            ['candidate_id' => $candidateInfo->id, 'degree_level' => 3],
            [
                'degree_name' => 'BS Computer Science', 'subject_major' => 'Software',
                'institution' => 'PUCIT', 'passing_year' => 2020, 'marks_type' => 'CGPA',
                'obtained_marks' => 3.8, 'total_marks' => 4.0,
            ]
        );

        // 2. Bulk Generation (50 Candidates)
        $this->command->info("Seeding 50 Bulk Candidates...");
        for ($i = 0; $i < 50; $i++) {
            $user = User::create([
                'first_name' => 'Candidate', 'last_name' => '#' . ($i + 1),
                'email' => "candidate." . ($i + 1) . "@example.com",
                'cnic' => '55555' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'phone' => '0300' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'password' => Hash::make(env('USER_DEFAULT_PASSWORD', 'password')), 'nationality' => 'Pakistani',
                'phone_verified_at' => now(), 'email_verified_at' => now(),
            ]);
            $user->assignRole('candidate');

            $cand = Candidate::create([
                'user_id' => $user->id, 'father_name' => "Father Name", 'gender' => rand(0, 1) ? 'Male' : 'Female',
                'dob' => Carbon::now()->subYears(rand(22, 35))->format('Y-m-d'),
                'domicile_city_id' => $cities[array_rand($cities)]->id, 
                'address_city_id' => $cities[array_rand($cities)]->id,
                'profile_locked' => true,
            ]);

            EducationHistory::create([
                'candidate_id' => $cand->id, 'degree_level' => 3, 'degree_name' => 'Bachelor Degree',
                'subject_major' => 'General', 'institution' => 'University', 'passing_year' => 2021,
                'marks_type' => 'Marks', 'obtained_marks' => 850, 'total_marks' => 1100,
            ]);

            // Apply to one job
            $job = $projectData['jobs'][array_rand($projectData['jobs'])];
            $app = Application::create([
                'candidate_id' => $cand->id, 'project_id' => $job->project_id, 'job_id' => $job->id,
                'desired_test_city_id' => $cand->domicile_city_id, 'status' => 'fee_paid', 'applied_at' => now(),
            ]);

            Payment::create([
                'application_id' => $app->id, 'challan_ref' => 'PAY-' . str_pad($app->id, 8, '0', STR_PAD_LEFT),
                'amount' => $job->fee, 'status' => 'paid', 'deposit_date' => now(),
            ]);
        }
    }
}
