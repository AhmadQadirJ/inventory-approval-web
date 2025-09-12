<?php

namespace App\Http\Controllers;

use App\Models\LendSubmission;
use App\Models\ProcureSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HistoryController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Ambil data dari kedua tabel untuk user saat ini
        $lendSubmissions = LendSubmission::where('user_id', $userId)->get();
        $procureSubmissions = ProcureSubmission::where('user_id', $userId)->get();

        // 2. Format dan gabungkan kedua koleksi data
        $submissions = $lendSubmissions->map(function ($item) {
            return (object) [
                'id' => $item->proposal_id,
                'type' => 'Peminjaman',
                'item' => $item->item_name,
                'purpose' => $item->purpose_title,
                'date' => $item->created_at->format('d/m/Y'),
                'status' => $item->status,
            ];
        })->merge($procureSubmissions->map(function ($item) {
            return (object) [
                'id' => $item->proposal_id,
                'type' => 'Pengadaan',
                'item' => $item->item_name,
                'purpose' => $item->purpose_title,
                'date' => $item->created_at->format('d/m/Y'),
                'status' => $item->status,
            ];
        }))->sortByDesc('date'); // Urutkan berdasarkan tanggal terbaru

        // 3. Hitung statistik untuk cards
        $pendingCount = $submissions->where('status', 'Pending')->count();
        $acceptedCount = $submissions->where('status', 'Accepted')->count();

        // Hitung status yang diawali dengan "Rejected"
        $rejectedCount = $submissions->filter(function ($item) {
            return Str::startsWith($item->status, 'Rejected');
        })->count();

        // Hitung status yang diawali dengan "Processed"
        $processedCount = $submissions->filter(function ($item) {
            return Str::startsWith($item->status, 'Processed');
        })->count();

        // 4. Kirim semua data ke view
        return view('history.index', [
            'submissions' => $submissions,
            'pendingCount' => $pendingCount,
            'acceptedCount' => $acceptedCount,
            'rejectedCount' => $rejectedCount,
            'processedCount' => $processedCount,
        ]);
    }

    public function show($proposal_id)
    {
        $submission = null;
        $type = null;

        // Tentukan tipe submission berdasarkan prefix ID
        if (Str::startsWith($proposal_id, 'A-')) {
            $submission = LendSubmission::where('proposal_id', $proposal_id)->first();
            $type = 'Peminjaman';
        } elseif (Str::startsWith($proposal_id, 'B-')) {
            $submission = ProcureSubmission::where('proposal_id', $proposal_id)->first();
            $type = 'Pengadaan';
        }

        // Jika submission tidak ditemukan, atau user mencoba melihat milik orang lain, tampilkan 404
        if (!$submission || $submission->user_id !== Auth::id()) {
            abort(404);
        }

        // Tambahkan properti 'type' ke objek untuk digunakan di view
        $submission->type = $type;

        return view('history.show', [
            'submission' => $submission
        ]);
    }
}