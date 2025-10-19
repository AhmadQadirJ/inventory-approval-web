<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): \Illuminate\View\View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // --- MULAI PERUBAHAN DI SINI ---

        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'nip' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // --- UBAH ATURAN VALIDASI DI SINI ---
            'branch' => ['required', 'string', Rule::in(['Jakarta', 'Bandung', 'Surabaya'])],
            'department' => ['required', 'string', Rule::in([
                'Operational',
                'Human Resources',
                'General Affair',
                'Finance and Acc Tax',
                'Technology',
                'Marketing & Creative'
            ])],
            'role' => ['required', 'string', 'in:Karyawan'],
        ]);

        $user = User::create([
            // 4. Hapus 'name', tambahkan field baru
            'name' => $request->username,
            'email' => $request->email,
            'nip' => $request->nip,
            'password' => Hash::make($request->password),
            'branch' => $request->branch,
            'department' => $request->department,
            'role' => $request->role,
        ]);

        // --- SELESAI PERUBAHAN ---

        event(new Registered($user));

        Auth::login($user);

        // Arahkan ke /dashboard sesuai kode Anda
        return redirect('/dashboard');
    }
}