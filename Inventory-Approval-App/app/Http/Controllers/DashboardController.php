<?php

namespace App\Http\Controllers;

use App\Models\LendSubmission;
use App\Models\ProcureSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Ambil data Peminjaman (Lend) dengan relasi ke Inventory (Eager Loading)
        $lendSubmissions = LendSubmission::with('inventory')->where('user_id', $userId)->get();
        
        // Ambil data Pengadaan (Procure)
        $procureSubmissions = ProcureSubmission::where('user_id', $userId)->get();

        // 2. Gabungkan semua data, pastikan Peminjaman mengambil nama dari relasi
        $allSubmissions = $lendSubmissions->map(function ($item) {
            return (object) [
                'id' => $item->proposal_id,
                'type' => 'Peminjaman',
                'item' => $item->inventory?->nama, // <-- PERUBAHAN UTAMA
                'purpose' => $item->purpose_title,
                'date' => $item->created_at,
                'status' => $item->status,
            ];
        })->merge($procureSubmissions->map(function ($item) {
            return (object) [
                'id' => $item->proposal_id,
                'type' => 'Pembelian',
                'item' => $item->item_name,
                'purpose' => $item->purpose_title,
                'date' => $item->created_at,
                'status' => $item->status,
            ];
        }));

        // 3. Ambil data terbaru untuk tabel "Latest Submission"
        $latestSubmissions = $allSubmissions->sortByDesc('date')->take(5)->map(function($item) {
            $item->date = $item->date->format('d/m/Y');
            return $item;
        });

        // 4. Hitung statistik untuk cards (akan otomatis benar)
        $pendingCount = $allSubmissions->where('status', 'Pending')->count();
        $acceptedCount = $allSubmissions->where('status', 'Accepted')->count();
        $rejectedCount = $allSubmissions->filter(fn($item) => Str::startsWith($item->status, 'Rejected'))->count();
        $processedCount = $allSubmissions->filter(fn($item) => Str::startsWith($item->status, 'Processed'))->count();

        // 5. Kirim semua data ke view
        return view('dashboard', [
            'latestSubmissions' => $latestSubmissions,
            'pendingCount' => $pendingCount,
            'acceptedCount' => $acceptedCount,
            'rejectedCount' => $rejectedCount,
            'processedCount' => $processedCount,
        ]);
    }
}