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

        echo "\n✅ PATS Seeder Complete!\n";
        echo "   Super Admin: admin@pats.test / Admin@1234 / CNIC: 0000000000001\n";
    }
}
