<?php

namespace App\Http\Controllers;

use App\Models\LendSubmission;
use App\Models\ProcureSubmission;
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
}