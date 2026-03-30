<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::role(['admin', 'data_entry', 'super_admin', 'examiner'])->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['admin', 'data_entry', 'super_admin', 'examiner'])->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:80',
            'last_name'  => 'required|string|max:80',
            'cnic'       => 'nullable|regex:/^\d{5}-\d{7}-\d{1}$/|unique:users',
            'phone'      => 'required|string|max:15',
            'email'      => 'nullable|email|unique:users',
            'role'       => 'required|exists:roles,name',
            'password'   => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'cnic'       => $data['cnic'],
            'phone'      => $data['phone'],
            'email'      => $data['email'],
            'nationality' => 'Pakistani',
            'phone_verified_at' => now(),
            'password'   => Hash::make($data['password']),
        ]);
        $user->assignRole($data['role']);

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        $roles = Role::whereIn('name', ['admin', 'data_entry', 'super_admin', 'examiner'])->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:80',
            'last_name'  => 'required|string|max:80',
            'cnic'       => ['nullable', 'regex:/^\d{5}-\d{7}-\d{1}$/', Rule::unique('users')->ignore($user)],
            'phone'      => 'required|string|max:15',
            'email'      => ['nullable', 'email', Rule::unique('users')->ignore($user)],
            'role'       => 'required|exists:roles,name',
            'password'   => 'nullable|min:8|confirmed',
            'is_active'  => 'nullable|boolean',
        ]);

        $user->update([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'cnic'       => $data['cnic'],
            'phone'      => $data['phone'],
            'email'      => $data['email'],
            'is_active'  => $request->boolean('is_active'),
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function toggle(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot deactivate yourself.');
        }
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'User status toggled.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete yourself.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
