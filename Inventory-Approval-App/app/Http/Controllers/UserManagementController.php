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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role_filter');
        $branchFilter = $request->input('branch_filter');

        // Mulai query dengan mengambil semua user kecuali admin saat ini
        $query = User::where('id', '!=', auth()->id());

        // Terapkan filter pencarian jika ada
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

        // Terapkan filter role jika ada
        if ($roleFilter) {
            $query->where('role', $roleFilter);
        }

        if ($branchFilter) {
            $query->where('branch', $branchFilter);
        }

        // Ambil hasil yang sudah difilter dan lakukan paginasi
        $users = $query->paginate(10)->withQueryString(); // withQueryString() agar filter tetap ada saat pindah halaman

        // Daftar role untuk dropdown
       $roles = ['Karyawan', 'General Affair', 'Finance', 'COO', 'CHRD', 'Admin'];
       $branches = ['Pusat', 'Bandung', 'Jakarta', 'Surabaya'];

       return view('user-management.index', compact('users', 'roles', 'branches'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user_management)
    {
        $user = $user_management;
        
        // Daftar untuk dropdown
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user_management)
    {
        $user = $user_management;
        $managementRoles = ['General Affair', 'Finance', 'COO', 'CHRD'];
        $rolesWithDepartment = ['Karyawan', 'General Affair', 'Finance'];

        // Validasi
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'nip' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string'],
            
            // Branch wajib jika Karyawan ATAU Management
            'branch' => [
                Rule::requiredIf(fn () => $request->role === 'Karyawan' || in_array($request->role, $managementRoles)),
                'nullable', 'string'
            ],
            // Department: Wajib HANYA JIKA role-nya 'Karyawan', 'GA', atau 'Finance'
            'department' => [
                Rule::requiredIf(fn () => in_array($request->role, $rolesWithDepartment)),
                'nullable', 'string'
            ],

            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'profile_photo' => ['nullable', 'image', 'max:1024'],
        ]);

        // Update Informasi Utama
        if ($request->filled('name')) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->nip = $request->nip;
            $user->role = $request->role;

            // Logika kondisional baru
            if (in_array($request->role, $rolesWithDepartment)) {
                // Karyawan, GA, Finance: Menyimpan keduanya
                $user->branch = $request->branch;
                $user->department = $request->department;
            } elseif (in_array($request->role, ['COO', 'CHRD'])) {
                // COO, CHRD: Menyimpan Branch, tapi MENGOSONGKAN Departemen
                $user->branch = $request->branch;
                $user->department = null;
            } else {
                // Role lain (Admin): Mengosongkan keduanya
                $user->branch = null;
                $user->department = null;
            }
        }

        // Update Password
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Update Foto
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user_management)
    {
        $user = $user_management;
        // Hapus foto dari storage jika ada
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        $user->delete();
        return redirect()->route('user-management.index')->with('success', 'User deleted successfully.');
    }
}
