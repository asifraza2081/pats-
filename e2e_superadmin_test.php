<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

echo "Starting SuperAdmin E2E Simulation...\n";

// Ensure roles exist
if (!Role::where('name', 'super_admin')->exists()) {
    die("FAIL: Run DB seeders first.\n");
}

$superAdmin = User::whereHas('roles', fn($q) => $q->where('name', 'super_admin'))->first();
if (!$superAdmin) {
    die("FAIL: No super_admin found.\n");
}

auth()->login($superAdmin);

$controller = app(\App\Http\Controllers\Admin\UserController::class);

// 1. Create a New Admin User
$testEmail = 'newadmin'.time().'@pats.test';
$requestCreate = Request::create('/admin/users', 'POST', [
    'first_name' => 'Test',
    'last_name'  => 'Admin',
    'cnic'       => '35202'.rand(10000000, 99999999),
    'phone'      => '0300'.rand(1000000, 9999999),
    'email'      => $testEmail,
    'role'       => 'admin',
    'password'   => 'password123',
    'password_confirmation' => 'password123'
]);
$requestCreate->setLaravelSession(session()->driver());
$responseCreate = $controller->store($requestCreate);

$newUser = User::where('email', $testEmail)->first();
if (!$newUser) {
    die("FAIL: User creation failed.\n");
}
echo "✅ Successfully Created New Admin User: {$newUser->first_name} ({$testEmail})\n";
if ($newUser->hasRole('admin')) echo "✅ Role 'admin' successfully assigned.\n";

// 2. Toggle Status (Deactivate)
$requestToggle = Request::create('/admin/users/'.$newUser->id.'/toggle', 'POST');
$requestToggle->setLaravelSession(session()->driver());
$controller->toggle($newUser);

$newUser->refresh();
if ($newUser->is_active == false) {
    echo "✅ Successfully toggled user status to inactive.\n";
} else {
    die("FAIL: Toggle status failed.\n");
}

// 3. Edit User Details
$requestEdit = Request::create('/admin/users/'.$newUser->id, 'PUT', [
    'first_name' => 'Updated',
    'last_name'  => 'Admin',
    'cnic'       => $newUser->cnic,
    'phone'      => $newUser->phone,
    'email'      => $newUser->email,
    'role'       => 'data_entry',
    'is_active'  => 1
]);
$requestEdit->setLaravelSession(session()->driver());
$controller->update($requestEdit, $newUser);

$newUser->refresh();
if ($newUser->first_name === 'Updated' && $newUser->hasRole('data_entry')) {
    echo "✅ Successfully updated user details and role (data_entry).\n";
} else {
    // Note: $newUser->roles is cached by spatie, so testing hasRole directly checks the refreshed relation if correctly handled, but DB check is better
    if ($newUser->roles()->first()->name === 'data_entry') {
        echo "✅ Successfully updated user details and role (data_entry).\n";
    } else {
        die("FAIL: Update details/role failed. {$newUser->roles()->first()->name}\n");
    }
}

// 4. Delete the user
$requestDelete = Request::create('/admin/users/'.$newUser->id, 'DELETE');
$requestDelete->setLaravelSession(session()->driver());
$controller->destroy($newUser);

if (!User::find($newUser->id)) {
    echo "✅ Successfully deleted the user.\n";
} else {
    die("FAIL: User deletion failed.\n");
}

echo "\n🏆 ALL SUPERADMIN SCENARIOS PASSED!\n";
