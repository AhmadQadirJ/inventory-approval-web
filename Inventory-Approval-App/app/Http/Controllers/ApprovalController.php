<?php

namespace App\Http\Controllers;

use App\Models\LendSubmission;
use App\Models\ProcureSubmission;
use App\Models\SubmissionTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $waitingOnly = $request->input('waiting'); // Ambil status checkbox

        $lendSubmissionsQuery = LendSubmission::query();
        $procureSubmissionsQuery = ProcureSubmission::query();

        if ($search) {
            // ... (logika pencarian tetap sama)
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

        $submissions = $lendSubmissions->map(function ($item) {
            // ... (mapping data tetap sama)
            return (object) [
                'id' => $item->proposal_id, 'type' => 'Peminjaman', 'item' => $item->item_name,
                'purpose' => $item->purpose_title, 'date' => $item->created_at->format('d/m/Y'),
                'status' => $item->status,
            ];
        })->merge($procureSubmissions->map(function ($item) {
            return (object) [
                'id' => $item->proposal_id, 'type' => 'Pengadaan', 'item' => $item->item_name,
                'purpose' => $item->purpose_title, 'date' => $item->created_at->format('d/m/Y'),
                'status' => $item->status,
            ];
        }));

        // -- LOGIKA BARU UNTUK FILTER "WAITING FOR APPROVAL" --
        $userRole = auth()->user()->role;
        if ($waitingOnly) {
            $submissions = $submissions->filter(function ($submission) use ($userRole) {
                switch ($userRole) {
                    case 'General Affair':
                        return in_array($submission->status, ['Pending', 'Processed - GA']);
                    case 'Manager':
                        return $submission->status === 'Processed - Manager';
                    case 'Finance':
                        return $submission->status === 'Processed - Finance';
                    case 'COO':
                        return $submission->status === 'Processed - COO';
                    default:
                        return false;
                }
            });
        }
        // -- AKHIR LOGIKA BARU --

        $submissions = $submissions->sortByDesc('date');

        // ... (logika statistik tetap sama)
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

            return redirect()->route('approval')->with('success', 'Proposal ' . $proposal_id . ' has been acted upon.');
        }

        // Jika tidak, kembalikan dengan pesan error
        return redirect()->route('approval')->with('error', 'Invalid action or proposal not found.');
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

        $currentStatus = $submission->status; // Simpan status saat ini SEBELUM diubah
        $userRole = auth()->user()->role;

         if (($userRole === 'General Affair' && $currentStatus !== 'Processed - GA') ||
            ($userRole === 'Manager' && $currentStatus !== 'Processed - Manager') ||
            ($userRole === 'Finance' && $currentStatus !== 'Processed - Finance') ||
            ($userRole === 'COO' && $currentStatus !== 'Processed - COO')) {
            return redirect()->route('approval')->with('error', 'Unauthorized action for your role.');
        }

        // Tentukan status berikutnya
        $nextStatus = [
            'Processed - GA' => 'Processed - Manager',
            'Processed - Manager' => 'Processed - Finance',
            'Processed - Finance' => 'Processed - COO',
            'Processed - COO' => 'Accepted',
        ];

        $newStatus = $nextStatus[$currentStatus] ?? $currentStatus;
        $submission->status = $newStatus;
        $submission->save();

        // Simpan status LAMA (yang baru selesai) ke timeline
        SubmissionTimeline::create([
            'submission_id' => $submission->id,
            'submission_type' => $submissionType,
            'status' => $currentStatus, // <-- PERUBAHAN UTAMA
            'notes' => $request->notes,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('approval')->with('success', "Proposal $proposal_id has been approved for the next step.");
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

        return redirect()->route('approval')->with('success', "Proposal $proposal_id has been rejected.");
    }
}