<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Setup Password Default
        $defaultPassword = Hash::make('123456789');

        // 2. Buat User ADMIN (Ahmad)
        User::create([
            'name' => 'ahmad', // Sesuai request awal (lowercase) atau mau disamakan 'Ahmad'? Saya biarkan default dulu.
            'email' => 'ahmadqadirmakassar@gmail.com',
            'password' => Hash::make('Generasi18@'), 
            'nip' => 'ADM001',
            'role' => 'Admin',
            'branch' => null,
            'department' => null, 
            'email_verified_at' => now(),
        ]);

        // ==========================================
        // 3. DATA USERS SPESIFIK (KARYAWAN, GA, FINANCE)
        // ==========================================
        
        $usersData = [
            // --- ROLE: KARYAWAN (BANDUNG) ---
            ['name' => 'Asep Sunandar',   'email' => 'asep@gmail.com',   'nip' => 'KRY-2401', 'role' => 'Karyawan', 'branch' => 'Bandung', 'department' => 'Operational'],
            ['name' => 'Lilis Suryani',   'email' => 'lilis@gmail.com',  'nip' => 'KRY-2402', 'role' => 'Karyawan', 'branch' => 'Bandung', 'department' => 'Human Resources'],
            ['name' => 'Dadang Hidayat',  'email' => 'dadang@gmail.com', 'nip' => 'KRY-2403', 'role' => 'Karyawan', 'branch' => 'Bandung', 'department' => 'Finance and Acc Tax'],
            ['name' => 'Rizky Fauzi',     'email' => 'rizky@gmail.com',  'nip' => 'KRY-2404', 'role' => 'Karyawan', 'branch' => 'Bandung', 'department' => 'Technology'],
            ['name' => 'Siska Putri',     'email' => 'siska@gmail.com',  'nip' => 'KRY-2405', 'role' => 'Karyawan', 'branch' => 'Bandung', 'department' => 'Marketing & Creative'],

            // --- ROLE: KARYAWAN (JAKARTA) ---
            ['name' => 'Budi Santoso',    'email' => 'budi@gmail.com',    'nip' => 'KRY-2406', 'role' => 'Karyawan', 'branch' => 'Jakarta', 'department' => 'Operational'],
            ['name' => 'Citra Lestari',   'email' => 'citra@gmail.com',   'nip' => 'KRY-2407', 'role' => 'Karyawan', 'branch' => 'Jakarta', 'department' => 'Human Resources'],
            ['name' => 'Eko Prasetyo',    'email' => 'eko@gmail.com',     'nip' => 'KRY-2408', 'role' => 'Karyawan', 'branch' => 'Jakarta', 'department' => 'Finance and Acc Tax'],
            ['name' => 'Kevin Wijaya',    'email' => 'kevin@gmail.com',   'nip' => 'KRY-2409', 'role' => 'Karyawan', 'branch' => 'Jakarta', 'department' => 'Technology'],
            ['name' => 'Jessica Tan',     'email' => 'jessica@gmail.com', 'nip' => 'KRY-2410', 'role' => 'Karyawan', 'branch' => 'Jakarta', 'department' => 'Marketing & Creative'],

            // --- ROLE: KARYAWAN (SURABAYA) ---
            ['name' => 'Joko Susilo',       'email' => 'joko@gmail.com',    'nip' => 'KRY-2411', 'role' => 'Karyawan', 'branch' => 'Surabaya', 'department' => 'Operational'],
            ['name' => 'Sari Wahyuni',      'email' => 'sari@gmail.com',    'nip' => 'KRY-2412', 'role' => 'Karyawan', 'branch' => 'Surabaya', 'department' => 'Human Resources'],
            ['name' => 'Bambang Pamungkas', 'email' => 'bambang@gmail.com', 'nip' => 'KRY-2413', 'role' => 'Karyawan', 'branch' => 'Surabaya', 'department' => 'Finance and Acc Tax'],
            ['name' => 'Bayu Nugroho',      'email' => 'bayu@gmail.com',    'nip' => 'KRY-2414', 'role' => 'Karyawan', 'branch' => 'Surabaya', 'department' => 'Technology'],
            ['name' => 'Devi Anggraeni',    'email' => 'devi@gmail.com',    'nip' => 'KRY-2415', 'role' => 'Karyawan', 'branch' => 'Surabaya', 'department' => 'Marketing & Creative'],

            // --- ROLE: GENERAL AFFAIR ---
            ['name' => 'Cecep Supriatna', 'email' => 'cecep@gmail.com',   'nip' => 'GA-1001', 'role' => 'General Affair', 'branch' => 'Bandung',  'department' => 'General Affair'],
            ['name' => 'Doni Monardo',    'email' => 'doni@gmail.com',    'nip' => 'GA-1002', 'role' => 'General Affair', 'branch' => 'Jakarta',  'department' => 'General Affair'],
            ['name' => 'Slamet Riyadi',   'email' => 'slamet@gmail.com',  'nip' => 'GA-1003', 'role' => 'General Affair', 'branch' => 'Surabaya', 'department' => 'General Affair'],
            ['name' => 'Hartono Sudibyo', 'email' => 'hartono@gmail.com', 'nip' => 'GA-1004', 'role' => 'General Affair', 'branch' => 'Pusat',    'department' => 'General Affair'],

            // --- ROLE: FINANCE (Lead/Khusus) ---
            ['name' => 'Ratna Juwita',    'email' => 'ratna@gmail.com',   'nip' => 'FIN-9001', 'role' => 'Finance', 'branch' => 'Bandung',  'department' => 'Finance and Acc Tax'],
            ['name' => 'Robert Salim',    'email' => 'robert@gmail.com',  'nip' => 'FIN-9002', 'role' => 'Finance', 'branch' => 'Jakarta',  'department' => 'Finance and Acc Tax'],
            ['name' => 'Susi Susanti',    'email' => 'susi@gmail.com',    'nip' => 'FIN-9003', 'role' => 'Finance', 'branch' => 'Surabaya', 'department' => 'Finance and Acc Tax'],
            ['name' => 'Handoko Gunawan', 'email' => 'handoko@gmail.com', 'nip' => 'FIN-9004', 'role' => 'Finance', 'branch' => 'Pusat',    'department' => 'Finance and Acc Tax'],
        ];

        // Eksekusi Loop Pembuatan User
        foreach ($usersData as $userData) {
            User::create([
                'name'              => $userData['name'],
                'email'             => $userData['email'],
                'password'          => $defaultPassword,
                'nip'               => $userData['nip'],
                'role'              => $userData['role'],
                'branch'            => $userData['branch'],
                'department'        => $userData['department'], 
                'email_verified_at' => now(),
            ]);
        }

        // ==========================================
        // 4. GENERATE USER COO & CHRD (CUSTOM NAME)
        // ==========================================
        
        // COO - Irwan Sudjatmiko
        User::create([
            'name' => 'Irwan Sudjatmiko',
            'email' => 'irwan@gmail.com',
            'password' => $defaultPassword,
            'nip' => 'COO001',
            'role' => 'COO',
            'branch' => 'Pusat',
            'department' => null, 
            'email_verified_at' => now(),
        ]);

        // CHRD - Indah Permatasari
        User::create([
            'name' => 'Indah Permatasari',
            'email' => 'indah@gmail.com',
            'password' => $defaultPassword,
            'nip' => 'CHRD001',
            'role' => 'CHRD',
            'branch' => 'Pusat',
            'department' => null,
            'email_verified_at' => now(),
        ]);
    }
}