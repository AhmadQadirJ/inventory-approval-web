<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use App\Models\User;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role_filter');
        $branchFilter = $request->input('branch_filter');

        $query = User::where('id', '!=', auth()->id());

       if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('branch', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($roleFilter) {
            $query->where('role', $roleFilter);
        }

        if ($branchFilter) {
            $query->where('branch', $branchFilter);
        }

        $users = $query->paginate(10)->withQueryString();

       $roles = ['Karyawan', 'General Affair', 'Finance', 'COO', 'CHRD', 'Admin'];
       $branches = ['Pusat', 'Bandung', 'Jakarta', 'Surabaya'];

       return view('user-management.index', compact('users', 'roles', 'branches'));
}
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(User $user_management)
    {
        $user = $user_management;

        $roles = ['Karyawan', 'General Affair', 'Finance', 'COO', 'CHRD', 'Admin'];
        $karyawan_branches = ['Bandung', 'Jakarta', 'Surabaya'];
        $management_branches = ['Pusat', 'Jakarta', 'Bandung', 'Surabaya'];
        $karyawan_departments = ['Operational', 'Human Resources', 'General Affair', 'Finance and Acc Tax', 'Technology', 'Marketing & Creative'];

        return view('user-management.edit', compact(
            'user', 
            'roles', 
            'karyawan_branches', 
            'management_branches',
            'karyawan_departments'
        ));
    }

    public function update(Request $request, User $user_management)
    {
        $user = $user_management;
        $managementRoles = ['General Affair', 'Finance', 'COO', 'CHRD'];
        $rolesWithDepartment = ['Karyawan', 'General Affair', 'Finance'];

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'nip' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string'],

            'branch' => [
                Rule::requiredIf(fn () => $request->role === 'Karyawan' || in_array($request->role, $managementRoles)),
                'nullable', 'string'
            ],
 
            'department' => [
                Rule::requiredIf(fn () => in_array($request->role, $rolesWithDepartment)),
                'nullable', 'string'
            ],

            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'profile_photo' => ['nullable', 'image', 'max:1024'],
        ]);

        if ($request->filled('name')) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->nip = $request->nip;
            $user->role = $request->role;

            if (in_array($request->role, $rolesWithDepartment)) {
                $user->branch = $request->branch;
                $user->department = $request->department;
            } elseif (in_array($request->role, ['COO', 'CHRD'])) {
                $user->branch = $request->branch;
                $user->department = null;
            } else {
                $user->branch = null;
                $user->department = null;
            }
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();
        return redirect()->route('user-management.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user_management)
    {
        $user = $user_management;
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        $user->delete();
        return redirect()->route('user-management.index')->with('success', 'User deleted successfully.');
    }
}
