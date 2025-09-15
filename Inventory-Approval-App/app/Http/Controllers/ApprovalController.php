<?php

namespace App\Http\Controllers;

use App\Models\LendSubmission;
use App\Models\ProcureSubmission;
use App\Models\SubmissionTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class ApprovalController extends Controller
{
    public function index()
    {
        // Ambil SEMUA data, tidak hanya milik user saat ini
        $lendSubmissions = LendSubmission::all();
        $procureSubmissions = ProcureSubmission::all();

        // Gabungkan dan format data
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
        }))->sortByDesc('date');

        // -- LOGIKA BARU UNTUK STATISTIK --
        $waitingForApprovalCount = 0;
        $userRole = auth()->user()->role;

        switch ($userRole) {
            case 'General Affair':
                // GA menghitung proposal yang butuh di-"Act" (Pending) dan di-"Proceed" (Processed - GA)
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
        // -- AKHIR LOGIKA BARU --

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
    
    // Perbaikan: Tambahkan logika pencarian submission berdasarkan proposal_id
    if (Str::startsWith($proposal_id, 'A-')) {
        $submission = LendSubmission::where('proposal_id', $proposal_id)->first();
        $submissionType = 'lend';
    } elseif (Str::startsWith($proposal_id, 'B-')) {
        $submission = ProcureSubmission::where('proposal_id', $proposal_id)->first();
        $submissionType = 'procure';
    }

    // Perbaikan: Periksa jika submission tidak ditemukan
    if (!$submission) {
        return redirect()->back()->with('error', 'Submission not found.');
    }

    // Simpan status saat ini SEBELUM diubah
    $currentStatus = $submission->status; 

    // Tentukan status penolakan baru berdasarkan peran pengguna
    $userRole = auth()->user()->role;
    $newStatus = "Rejected - " . explode(' ', $userRole)[0];
    
    // Perbarui status submission
    $submission->status = $newStatus;
    $submission->save();

    // Simpan status LAMA (yang baru selesai) ke timeline
    SubmissionTimeline::create([
        'submission_id' => $submission->id,
        'submission_type' => $submissionType,
        'status' => $currentStatus, // <-- Perubahan kunci: mencatat status lama
        'notes' => $request->notes,
        'user_id' => auth()->id(),
    ]);

    return redirect()->route('approval')->with('success', "Proposal $proposal_id has been rejected.");
}
}