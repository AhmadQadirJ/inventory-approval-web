<?php

namespace App\Http\Controllers;

use App\Models\LendSubmission;
use App\Models\ProcureSubmission;
use App\Models\SubmissionTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;


class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status_filter');
        $waitingOnly = $request->input('waiting');

        // Eager load relasi 'inventory' untuk efisiensi
        $lendSubmissionsQuery = LendSubmission::with('inventory');
        $procureSubmissionsQuery = ProcureSubmission::query();

        if ($search) {
            $lendSubmissionsQuery->where(function ($query) use ($search) {
                $query->where('proposal_id', 'like', "%{$search}%")
                    ->orWhere('purpose_title', 'like', "%{$search}%")
                    ->orWhereHas('inventory', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    });
            });
            $procureSubmissionsQuery->where(function ($query) use ($search) {
                $query->where('proposal_id', 'like', "%{$search}%")
                    ->orWhere('item_name', 'like', "%{$search}%")
                    ->orWhere('purpose_title', 'like', "%{$search}%");
            });
        }

        $lendSubmissions = $lendSubmissionsQuery->get();
        $procureSubmissions = $procureSubmissionsQuery->get();

        // Mapping data peminjaman
        $mappedLend = $lendSubmissions->map(fn($item) => (object) [
            'id' => $item->proposal_id,
            'type' => 'Peminjaman',
            'item' => $item->inventory?->nama, // Mengambil dari relasi
            'purpose' => $item->purpose_title,
            'date' => $item->created_at->format('d/m/Y'),
            'status' => $item->status
        ]);

        // Mapping data pengadaan
        $mappedProcure = $procureSubmissions->map(fn($item) => (object) [
            'id' => $item->proposal_id,
            'type' => 'Pengadaan',
            'item' => $item->item_name,
            'purpose' => $item->purpose_title,
            'date' => $item->created_at->format('d/m/Y'),
            'status' => $item->status
        ]);

        // Gabungkan kedua hasil mapping
        $submissions = new \Illuminate\Support\Collection(array_merge($mappedLend->all(), $mappedProcure->all()));

        $userRole = auth()->user()->role;
        if ($waitingOnly) {
            $submissions = $submissions->filter(function ($submission) use ($userRole) {
                switch ($userRole) {
                    case 'General Affair': return in_array($submission->status, ['Pending', 'Processed - GA']);
                    case 'Manager': return $submission->status === 'Processed - Manager';
                    case 'Finance': return $submission->status === 'Processed - Finance';
                    case 'COO': return $submission->status === 'Processed - COO';
                    default: return false;
                }
            });
        }

        if ($statusFilter) {
            $submissions = $submissions->filter(function ($submission) use ($statusFilter) {
                if (in_array($statusFilter, ['Rejected', 'Processed'])) {
                    return Str::startsWith($submission->status, $statusFilter);
                }
                return $submission->status === $statusFilter;
            });
        }

        $submissions = $submissions->sortByDesc('date');

        $waitingForApprovalCount = 0;
        switch ($userRole) {
            case 'General Affair':
                $waitingForApprovalCount = $submissions->whereIn('status', ['Pending', 'Processed - GA'])->count();
                break;
            case 'Manager':
                $waitingForApprovalCount = $submissions->where('status', 'Processed - Manager')->count();
                break;
            case 'Finance':
                $waitingForApprovalCount = $submissions->where('status', 'Processed - Finance')->count();
                break;
            case 'COO':
                $waitingForApprovalCount = $submissions->where('status', 'Processed - COO')->count();
                break;
        }

        return view('approval.index', [
            'submissions' => $submissions,
            'waitingForApprovalCount' => $waitingForApprovalCount,
        ]);
    }

    public function act($proposal_id)
    {
        // 1. Pastikan hanya General Affair yang bisa menjalankan aksi ini
        if (auth()->user()->role !== 'General Affair') {
            abort(403, 'Unauthorized action.');
        }

        $submission = null;

        // 2. Cari proposal di kedua tabel berdasarkan ID
        if (Str::startsWith($proposal_id, 'A-')) {
            $submission = LendSubmission::where('proposal_id', $proposal_id)->first();
        } elseif (Str::startsWith($proposal_id, 'B-')) {
            $submission = ProcureSubmission::where('proposal_id', $proposal_id)->first();
        }

        // 3. Jika proposal ditemukan dan statusnya 'Pending', ubah statusnya
        if ($submission && $submission->status === 'Pending') {
            $submission->status = 'Processed - GA';
            $submission->save();

            return redirect()->route('approval.index')->with('success', 'Proposal ' . $proposal_id . ' has been acted upon.');
        }

        // Jika tidak, kembalikan dengan pesan error
        return redirect()->route('approval.index')->with('error', 'Invalid action or proposal not found.');
    }

    public function process($proposal_id)
    {
        $submission = null;
        $type = null;

        if (Str::startsWith($proposal_id, 'A-')) {
            $submission = LendSubmission::where('proposal_id', $proposal_id)->firstOrFail();
            $type = 'Peminjaman';
        } elseif (Str::startsWith($proposal_id, 'B-')) {
            $submission = ProcureSubmission::where('proposal_id', $proposal_id)->firstOrFail();
            $type = 'Pengadaan';
        }
        $submission->type = $type;

        return view('approval.process', compact('submission'));
    }

    public function show($proposal_id)
    {
        $submission = null;
        $type = null;

        if (Str::startsWith($proposal_id, 'A-')) {
            $submission = LendSubmission::where('proposal_id', $proposal_id)->firstOrFail();
            $type = 'Peminjaman';
        } elseif (Str::startsWith($proposal_id, 'B-')) {
            $submission = ProcureSubmission::where('proposal_id', $proposal_id)->firstOrFail();
            $type = 'Pengadaan';
        } else {
            abort(404);
        }

        // Eager load relasi timeline dan user yang terkait
        $submission->load('timelines.user');

        // Tambahkan properti 'type' ke objek untuk digunakan di view
        $submission->type = $type;

        // Kita gunakan kembali view dari halaman history
        return view('history.show', [
            'submission' => $submission
        ]);
    }

    public function approve(Request $request, $proposal_id)
    {
        $submission = null;
        $submissionType = null;
        if (Str::startsWith($proposal_id, 'A-')) {
            $submission = LendSubmission::findOrFail($request->id);
            $submissionType = 'lend';
        } elseif (Str::startsWith($proposal_id, 'B-')) {
            $submission = ProcureSubmission::findOrFail($request->id);
            $submissionType = 'procure';
        }

        $currentStatus = $submission->status;

        // Tentukan status berikutnya
        $nextStatusMap = [
            'Processed - GA' => 'Processed - Manager',
            'Processed - Manager' => 'Processed - Finance',
            'Processed - Finance' => 'Processed - COO',
            'Processed - COO' => 'Accepted',
        ];

        $newStatus = $nextStatusMap[$currentStatus] ?? $currentStatus;

        // Update status utama proposal
        $submission->status = $newStatus;
        $submission->save();

        // Simpan status LAMA (yang baru selesai) ke timeline
        SubmissionTimeline::create([
            'submission_id' => $submission->id,
            'submission_type' => $submissionType,
            'status' => $currentStatus,
            'notes' => $request->notes,
            'user_id' => auth()->id(),
        ]);

        // --- PERUBAHAN UTAMA DI SINI ---
        // Jika status baru adalah "Accepted", buat satu entri timeline tambahan untuk menandakan proposal selesai.
        if ($newStatus === 'Accepted') {
            SubmissionTimeline::create([
                'submission_id' => $submission->id,
                'submission_type' => $submissionType,
                'status' => 'Accepted',
                'notes' => 'Proposal has been fully approved.',
                'user_id' => auth()->id(), // Dicatat oleh approver terakhir
            ]);
        }
        // --- AKHIR PERUBAHAN ---

        return redirect()->route('approval.index')->with('success', "Proposal $proposal_id has been approved for the next step.");
    }

    public function reject(Request $request, $proposal_id)
    {
        $submission = null;
        $submissionType = null;

        // LENGKAPI LOGIKA PENCARIAN INI
        if (Str::startsWith($proposal_id, 'A-')) {
            $submission = LendSubmission::findOrFail($request->id);
            $submissionType = 'lend';
        } elseif (Str::startsWith($proposal_id, 'B-')) {
            $submission = ProcureSubmission::findOrFail($request->id);
            $submissionType = 'procure';
        } else {
            abort(404);
        }
        // ---

        $currentStatus = $submission->status; 

        $newStatus = 'Rejected';
        $submission->status = $newStatus;
        $submission->save();

        SubmissionTimeline::create([
            'submission_id' => $submission->id,
            'submission_type' => $submissionType,
            'status' => $currentStatus,
            'notes' => $request->notes,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('approval.index')->with('success', "Proposal $proposal_id has been rejected.");
    }

    public function printPdf($proposal_id)
    {
        $submission = null;
        $type = null;

        if (Str::startsWith($proposal_id, 'A-')) {
            $submission = LendSubmission::where('proposal_id', $proposal_id)->first();
            $type = 'Peminjaman';
        } elseif (Str::startsWith($proposal_id, 'B-')) {
            $submission = ProcureSubmission::where('proposal_id', $proposal_id)->first();
            $type = 'Pengadaan';
        }

        // Pemeriksaan keamanan untuk approver: hanya cek status, BUKAN kepemilikan
        if (!$submission || $submission->status !== 'Accepted') {
            abort(403, 'Submission Not Accepted or Not Found.');
        }
        $submission->type = $type;

        $data = [
            'submission' => $submission,
            'document_number' => date('Y').'/INV/'.str_replace('-', '/', $submission->proposal_id)
        ];

        $pdf = Pdf::loadView('pdf.submission-document', $data);
        return $pdf->stream('submission-' . $submission->proposal_id . '.pdf');
    }

    public function printDetail($proposal_id)
    {
        // Logika untuk menemukan submission (sama seperti di method show)
        $submission = null; $type = null;
        if (Str::startsWith($proposal_id, 'A-')) {
            $submission = LendSubmission::where('proposal_id', $proposal_id)->firstOrFail(); $type = 'Peminjaman';
        } elseif (Str::startsWith($proposal_id, 'B-')) {
            $submission = ProcureSubmission::where('proposal_id', $proposal_id)->firstOrFail(); $type = 'Pengadaan';
        } else { abort(404); }
        $submission->load('timelines.user');
        $submission->type = $type;

        $pdf = Pdf::loadView('history.print-detail', ['submission' => $submission]);

        // Kita gunakan kembali view yang sama
        return $pdf->stream('detail-' . $submission->proposal_id . '.pdf');
    }
}