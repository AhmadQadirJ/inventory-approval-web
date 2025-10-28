<?php

namespace App\Http\Controllers;

use App\Models\LendSubmission;
use App\Models\ProcureSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $search = $request->input('search');
        $statusFilter = $request->input('status_filter');

        // 1. Ambil data Peminjaman (Lend) dengan relasi ke Inventory
        $lendSubmissionsQuery = LendSubmission::with('inventory')->where('user_id', $userId);
        
        // Ambil data Pengadaan (Procure)
        $procureSubmissionsQuery = ProcureSubmission::where('user_id', $userId);

        if ($search) {
            $lendSubmissionsQuery->where(function ($query) use ($search) {
                $query->where('proposal_id', 'like', "%{$search}%")
                    ->orWhere('purpose_title', 'like', "%{$search}%")
                    ->orWhere('branch', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereRaw("LOWER('Peminjaman') LIKE ?", ["%".strtolower($search)."%"])
                    ->orWhereHas('inventory', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    });
            });
            
            $procureSubmissionsQuery->where(function ($query) use ($search) {
                $query->where('proposal_id', 'like', "%{$search}%")
                    ->orWhere('item_name', 'like', "%{$search}%")
                    ->orWhere('purpose_title', 'like', "%{$search}%")
                    ->orWhere('branch', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereRaw("LOWER('Pengadaan') LIKE ?", ["%".strtolower($search)."%"]);
            });
        }

        $lendSubmissions = $lendSubmissionsQuery->get();
        $procureSubmissions = $procureSubmissionsQuery->get();

        // 2. Modifikasi mapping untuk Lend agar mengambil nama dari relasi
        $mappedLend = $lendSubmissions->map(fn($item) => (object) [
            'id' => $item->proposal_id,
            'type' => 'Peminjaman',
            'item' => $item->inventory?->nama, // <-- PERUBAHAN UTAMA
            'purpose' => $item->purpose_title,
            'date' => $item->created_at->format('d/m/Y'),
            'status' => $item->status
        ]);
        
        // Mapping untuk Procure tetap sama
        $mappedProcure = $procureSubmissions->map(fn($item) => (object) [
            'id' => $item->proposal_id,
            'type' => 'Pengadaan',
            'item' => $item->item_name,
            'purpose' => $item->purpose_title,
            'date' => $item->created_at->format('d/m/Y'),
            'status' => $item->status
        ]);

        $submissions = new Collection(array_merge($mappedLend->all(), $mappedProcure->all()));

        if ($statusFilter) {
            $submissions = $submissions->filter(function ($submission) use ($statusFilter) {
                return Str::contains($submission->status, $statusFilter);
            });
        }

        $submissions = $submissions->sortByDesc('date');

        $pendingCount = $submissions->filter(fn($item) => Str::startsWith($item->status, 'Pending'))->count();

        $acceptedCount = $submissions->filter(fn($item) =>
            Str::startsWith($item->status, 'Accepted')
        )->count();

        $rejectedCount = $submissions->filter(fn($item) =>
            Str::startsWith($item->status, 'Rejected')
        )->count();

        $processedCount = $submissions->filter(fn($item) =>
            Str::startsWith($item->status, 'Processed')
        )->count();

        return view('history.index', compact('submissions', 'pendingCount', 'acceptedCount', 'rejectedCount', 'processedCount'));
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

    public function printPdf($proposal_id)
    {
        $submission = null;
        $type = null;

        // Logika untuk menemukan submission (sama seperti di method show)
        if (Str::startsWith($proposal_id, 'A-')) {
            $submission = LendSubmission::where('proposal_id', $proposal_id)->first();
            $type = 'Peminjaman';
        } elseif (Str::startsWith($proposal_id, 'B-')) {
            $submission = ProcureSubmission::where('proposal_id', $proposal_id)->first();
            $type = 'Pengadaan';
        }

        // Pastikan user hanya bisa print proposal miliknya yang sudah 'Accepted'
        if (!$submission || $submission->user_id !== Auth::id() || !Str::startsWith($submission->status, 'Accepted')) {
            abort(403, 'Unauthorized Action or Submission Not Accepted.');
        }
        $submission->type = $type;

        // Data untuk dikirim ke view PDF
        $data = [
            'submission' => $submission,
            'document_number' => date('Y').'/INV/'.str_replace('-', '/', $submission->proposal_id)
        ];

        // Buat PDF dari view
        $pdf = Pdf::loadView('pdf.submission-document', $data);

        // Tampilkan PDF di browser
        return $pdf->stream('submission-' . $submission->proposal_id . '.pdf');
    }

    public function printDetail($proposal_id)
    {
        // Logika untuk menemukan submission (sama seperti di method show)
        $submission = null; $type = null;
        if (Str::startsWith($proposal_id, 'A-')) {
            $submission = LendSubmission::where('proposal_id', $proposal_id)->first(); $type = 'Peminjaman';
        } elseif (Str::startsWith($proposal_id, 'B-')) {
            $submission = ProcureSubmission::where('proposal_id', $proposal_id)->first(); $type = 'Pengadaan';
        }
        if (!$submission || $submission->user_id !== Auth::id()) { abort(404); }
        $submission->load('timelines.user');
        $submission->type = $type;

        $pdf = Pdf::loadView('history.print-detail', ['submission' => $submission]);

        return $pdf->stream('detail-' . $submission->proposal_id . '.pdf');
    }
}