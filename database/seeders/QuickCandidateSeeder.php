<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Candidate;
use App\Models\Application;
use App\Models\Project;
use App\Models\PatsJob;
use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class QuickCandidateSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'candidate', 'guard_name' => 'web']);

        $city    = City::first();
        $jobs    = PatsJob::limit(3)->get();
        $project = Project::first();

        if (!$city || !$project || $jobs->isEmpty()) {
            echo "ERROR: No cities/projects/jobs found. Run DatabaseSeeder first.\n";
            return;
        }

        for ($i = 0; $i < 10; $i++) {
            $email = 'candidate.flow' . $i . '@pats.test';
            $cnic  = '88888' . str_pad($i, 8, '0', STR_PAD_LEFT);

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'first_name'          => 'Candidate',
                    'last_name'           => 'FlowTest ' . $i,
                    'password'            => Hash::make(env('USER_DEFAULT_PASSWORD', 'password')),
                    'cnic'                => $cnic,
                    'phone'               => '0388' . str_pad($i, 7, '0', STR_PAD_LEFT),
                    'nationality'         => 'Pakistani',
                    'phone_verified_at'   => now(),
                    'email_verified_at'   => now(),
                ]
            );
            $user->syncRoles(['candidate']);

            $candidate = Candidate::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'father_name'       => 'Father Name ' . $i,
                    'gender'            => $i % 2 === 0 ? 'Male' : 'Female',
                    'dob'               => '1995-06-15',
                    'domicile_city_id'  => $city->id,
                    'address_city_id'   => $city->id,
                    'postal_address'    => 'House #' . ($i + 1) . ', Test Street, ' . $city->name,
                    'profile_locked'    => true,
                ]
            );

            $job = $jobs->get($i % $jobs->count());
            Application::updateOrCreate(
                ['candidate_id' => $candidate->id, 'job_id' => $job->id, 'project_id' => $project->id],
                [
                    'desired_test_city_id' => $city->id,
                    'status'               => 'fee_paid',
                    'applied_at'           => now()->subDays($i + 1),
                ]
            );
        }

        echo "\n✅ Quick Candidate Seeder Complete!\n";
        echo "   10 candidates created. Login: candidate.flow0@pats.test / password\n";
    }
}
