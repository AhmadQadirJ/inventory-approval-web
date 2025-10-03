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

        // 1. Ambil semua data submission untuk user saat ini
        $lendSubmissions = LendSubmission::where('user_id', $userId)->get();
        $procureSubmissions = ProcureSubmission::where('user_id', $userId)->get();

        // 2. Format data peminjaman
        $mappedLend = $lendSubmissions->map(function ($item) {
            return (object) [
                'id' => $item->proposal_id,
                'type' => 'Peminjaman',
                'item' => $item->item_name,
                'purpose' => $item->purpose_title,
                'date' => $item->created_at, // Gunakan objek Carbon untuk sorting
                'status' => $item->status,
            ];
        });

        // 3. Format data pengadaan
        $mappedProcure = $procureSubmissions->map(function ($item) {
            return (object) [
                'id' => $item->proposal_id,
                'type' => 'Pembelian',
                'item' => $item->item_name,
                'purpose' => $item->purpose_title,
                'date' => $item->created_at, // Gunakan objek Carbon untuk sorting
                'status' => $item->status,
            ];
        });

        // 4. Gabungkan sebagai koleksi dasar (bukan Eloquent Collection)
        $allSubmissions = new Collection(array_merge($mappedLend->all(), $mappedProcure->all()));

        // 5. Ambil 5 data terbaru untuk tabel "Latest Submission"
        $latestSubmissions = $allSubmissions->sortByDesc('date')->take(5)->map(function($item) {
            $item->date = $item->date->format('d/m/Y'); // Ubah format tanggal setelah sorting
            return $item;
        });

        // 6. Hitung statistik untuk cards
        $pendingCount = $allSubmissions->where('status', 'Pending')->count();
        $acceptedCount = $allSubmissions->where('status', 'Accepted')->count();
        $rejectedCount = $allSubmissions->filter(fn($item) => Str::startsWith($item->status, 'Rejected'))->count();
        $processedCount = $allSubmissions->filter(fn($item) => Str::startsWith($item->status, 'Processed'))->count();

        // 7. Kirim semua data ke view
        return view('dashboard', [
            'latestSubmissions' => $latestSubmissions,
            'pendingCount' => $pendingCount,
            'acceptedCount' => $acceptedCount,
            'rejectedCount' => $rejectedCount,
            'processedCount' => $processedCount,
        ]);
    }
}