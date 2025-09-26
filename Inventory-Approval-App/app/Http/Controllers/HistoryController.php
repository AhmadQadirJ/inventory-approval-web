<?php

namespace App\Http\Controllers;

use App\Models\LendSubmission;
use App\Models\ProcureSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $search = $request->input('search');
        $statusFilter = $request->input('status_filter'); // Ambil input filter status

        // 1. Ambil data dari kedua tabel, tambahkan filter pencarian jika ada
        $lendSubmissionsQuery = LendSubmission::where('user_id', $userId);
        $procureSubmissionsQuery = ProcureSubmission::where('user_id', $userId);

        if ($search) {
            // Logika pencarian tetap sama
            $lendSubmissionsQuery->where(function ($query) use ($search) {
                $query->where('proposal_id', 'like', "%{$search}%")
                    ->orWhere('item_name', 'like', "%{$search}%")
                    ->orWhere('purpose_title', 'like', "%{$search}%");
            });
            $procureSubmissionsQuery->where(function ($query) use ($search) {
                $query->where('proposal_id', 'like', "%{$search}%")
                    ->orWhere('item_name', 'like', "%{$search}%")
                    ->orWhere('purpose_title', 'like', "%{$search}%");
            });
        }

        $lendSubmissions = $lendSubmissionsQuery->get();
        $procureSubmissions = $procureSubmissionsQuery->get();

        // 2. Format dan gabungkan kedua koleksi data
        $submissions = $lendSubmissions->map(function ($item) {
            // ... (mapping data tetap sama)
            return (object) [ 'id' => $item->proposal_id, 'type' => 'Peminjaman', 'item' => $item->item_name, 'purpose' => $item->purpose_title, 'date' => $item->created_at->format('d/m/Y'), 'status' => $item->status ];
        })->merge($procureSubmissions->map(function ($item) {
            return (object) [ 'id' => $item->proposal_id, 'type' => 'Pengadaan', 'item' => $item->item_name, 'purpose' => $item->purpose_title, 'date' => $item->created_at->format('d/m/Y'), 'status' => $item->status ];
        }));

        // -- LOGIKA BARU UNTUK FILTER STATUS --
        if ($statusFilter) {
            $submissions = $submissions->filter(function ($submission) use ($statusFilter) {
                // Untuk "Rejected" dan "Processed", kita cek awal katanya saja
                if ($statusFilter === 'Rejected' || $statusFilter === 'Processed') {
                    return Str::startsWith($submission->status, $statusFilter);
                }
                // Untuk "Pending" dan "Accepted", kita cek kecocokan persis
                return $submission->status === $statusFilter;
            });
        }
        // -- AKHIR LOGIKA BARU --

        $submissions = $submissions->sortByDesc('date');

        // 3. Hitung statistik (tetap sama, akan dihitung berdasarkan data yang sudah difilter)
        $pendingCount = $submissions->where('status', 'Pending')->count();
        $acceptedCount = $submissions->where('status', 'Accepted')->count();
        $rejectedCount = $submissions->filter(fn($item) => Str::startsWith($item->status, 'Rejected'))->count();
        $processedCount = $submissions->filter(fn($item) => Str::startsWith($item->status, 'Processed'))->count();

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

        // logika untuk menemukan $submission
        $submission->load('timelines.user'); // Eager load relasi timeline dan user

        return view('history.show', [
            'submission' => $submission
        ]);
    }
}