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
        $project = \App\Models\Project::create([
            'name'        => 'Ministry of IT Recruitment 2026',
            'org_name'    => 'Ministry of Information Technology',
            'description' => 'Dummy project for e2e testing.',
            'open_date'   => now()->subDays(5),
            'close_date'  => now()->addDays(10),
            'status'      => 'open',
            'created_by'  => $superUser->id,
        ]);

        $job = \App\Models\PatsJob::create([
            'project_id'       => $project->id,
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

        $center = \App\Models\TestCenter::create([
            'tcid'           => 'LHE',
            'name'           => 'Expo Center Lahore',
            'city'           => 'Lahore',
            'province'       => 'Punjab',
            'address'        => 'Johar Town, Lahore',
            'total_capacity' => 500,
            'is_active'      => true,
        ]);
        $center->projects()->attach($project->id);

        $batch = \App\Models\Batch::create([
            'project_id'    => $project->id,
            'center_id'     => $center->id,
            'batch_number'  => 1,
            'test_date'     => now()->addDays(15),
            'reporting_time'=> '08:00:00',
            'start_time'    => '09:00:00',
            'total_seats'   => 100,
            'booked_seats'  => 0,
            'envelope_size' => 20,
        ]);

        echo "\n✅ PATS Seeder Complete with Dummy Data!\n";
        echo "   Super Admin: admin@pats.test / Admin@1234 / CNIC: 0000000000001\n";
    }
}
