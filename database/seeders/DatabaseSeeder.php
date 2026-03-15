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

        $superAdmin = Role::whereName('super_admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
        }

        $admin = Role::whereName('admin')->first();
        if ($admin) {
            $admin->syncPermissions(Permission::whereNot('name', 'manage users')->get());
        }

        $dataEntry = Role::whereName('data_entry')->first();
        if ($dataEntry) {
            $dataEntry->syncPermissions(
                Permission::whereIn('name', ['verify payments', 'mark attendance', 'view reports'])->get()
            );
        }

        $examiner = Role::whereName('examiner')->first();
        if ($examiner) {
            $examiner->syncPermissions(
                Permission::whereIn('name', ['view assigned sessions', 'mark attendance', 'view reports'])->get()
            );
        }

        // ── Admin Users ──────────────────────────────
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

        $dataEntryUser = User::updateOrCreate(
            ['email' => 'data@pats.test'],
            [
                'first_name'        => 'Data',
                'last_name'         => 'Entry',
                'cnic'              => '0000000000002',
                'phone'             => '03000000001',
                'nationality'       => 'Pakistani',
                'password'          => Hash::make('password'),
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
            ]
        );
        $dataEntryUser->assignRole('data_entry');
        
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

        // ── Projects ─────────────────────
        $project1 = \App\Models\Project::create([
            'name'        => 'National Health Drive 2026',
            'org_name'    => 'Ministry of Health',
            'description' => 'Medical and para-medical staff recruitment.',
            'open_date'   => now()->subDays(10),
            'close_date'  => now()->addDays(15),
            'status'      => 'open',
            'created_by'  => $superUser->id,
        ]);

        $job1 = \App\Models\PatsJob::create([
            'project_id'       => $project1->id,
            'job_code'         => 101,
            'title'            => 'Medical Officer',
            'department'       => 'Clinical',
            'bps_grade'        => 'BPS-17',
            'total_seats'      => 50,
            'fee'              => 1500,
            'min_degree_level' => 3, 
            'age_min'          => 22,
            'age_max'          => 40,
        ]);

        $center1 = \App\Models\TestCenter::create([
            'tcid'             => '9001',
            'name'             => 'PATS HQ Lahore',
            'city_id'          => $cityLhe->id,
            'address'          => 'Main Boulevard, Lahore',
            'seating_capacity' => 1000,
            'is_active'        => true,
        ]);
        $center1->projects()->attach($project1->id);

        // ── Candidate User (Fully Profiled) ──────────────────────────────
        $candidateUser = User::create([
            'first_name'        => 'Test',
            'last_name'         => 'Candidate',
            'cnic'              => '3520200000001',
            'phone'             => '03001234567',
            'email'             => 'candidate@pats.test',
            'nationality'       => 'Pakistani',
            'password'          => Hash::make('password'),
            'phone_verified_at' => now(),
        ]);
        $candidateUser->assignRole('candidate');

        $candidateInfo = \App\Models\Candidate::create([
            'user_id'              => $candidateUser->id,
            'father_name'          => 'John Doe Sr',
            'gender'               => 'Male',
            'dob'                  => '1998-05-15',
            'domicile_city_id'     => $cityLhe->id,
            'address_city_id'      => $cityLhe->id,
            'postal_address'       => 'House 123, Street 4, Lahore',
            'profile_locked'       => false,
            'religion'             => 'Islam',
            'province_of_domicile' => 'Punjab',
            'district_of_domicile' => 'Lahore',
        ]);

        \App\Models\EducationHistory::create([
            'candidate_id'    => $candidateInfo->id,
            'degree_level'    => 3, 
            'degree_name'     => 'MBBS',
            'subject_major'   => 'Medicine',
            'institution'     => 'King Edward Medical University',
            'passing_year'    => 2020,
            'marks_type'      => 'Marks',
            'obtained_marks'  => 850,
            'total_marks'     => 1100,
        ]);

        echo "\n✅ PATS Seeder Complete! Ready for Flow Testing.\n";
        echo "   Super Admin: admin@pats.test / Admin@1234\n";
        echo "   Candidate: candidate@pats.test / password\n";
        echo "   Examiner: examiner@pats.test / password\n";

    }
}
