<?php

namespace App\Http\Controllers;

use App\Models\LendSubmission;
use App\Models\ProcureSubmission;
use App\Models\SubmissionTimeline;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    // Menampilkan halaman pilihan submission
    public function index()
    {
        return view('submission.index');
    }

    // Menampilkan form peminjaman barang
    public function createLend()
    {
        return view('submission.lend-form');
    }

    // Menampilkan form pengadaan barang
    public function createProcure()
    {
        return view('submission.procure-form');
    }

    // Method untuk menyimpan data form Peminjaman
    public function storeLend(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nip' => 'required|string|max:255',
            'departemen' => 'required|string',
            'nama_barang' => 'required|string',
            'jumlah' => 'required|integer|min:1',
            'judul_peminjaman' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'deskripsi_peminjaman' => 'required|string|max:300',
        ]);

        // 1. Buat record submission tanpa proposal_id terlebih dahulu
        $submission = LendSubmission::create([
            'user_id' => auth()->id(),
            'full_name' => $validated['nama_lengkap'],
            'employee_id' => $validated['nip'],
            'department' => $validated['departemen'],
            'item_name' => $validated['nama_barang'],
            'quantity' => $validated['jumlah'],
            'purpose_title' => $validated['judul_peminjaman'],
            'start_date' => $validated['tanggal_mulai'],
            'end_date' => $validated['tanggal_selesai'],
            'description' => $validated['deskripsi_peminjaman'],
        ]);

        // 2. Buat proposal_id berdasarkan ID record yang baru dibuat, lalu simpan
        $submission->proposal_id = 'A-' . $submission->id;
        $submission->save();

        SubmissionTimeline::create([
            'submission_id' => $submission->id,
            'submission_type' => 'lend',
            'status' => 'Pending',
            'notes' => 'Submission created by user.',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('submission')->with('success', 'Pengajuan peminjaman barang (ID: ' . $submission->proposal_id . ') berhasil dikirim!');
    }

    // Method untuk menyimpan data form Pengadaan
    public function storeProcure(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nip' => 'required|string|max:255',
            'departemen' => 'required|string',
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'estimasi_harga' => 'required|min:0',
            'link_referensi' => 'nullable|url',
            'deskripsi_barang' => 'required|string|max:300',
            'judul_pengadaan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'deskripsi_pengadaan' => 'required|string|max:300',
        ]);

        $cleanPrice = str_replace('.', '', $request->estimasi_harga);
        // 1. Buat record submission
        $submission = ProcureSubmission::create([
            'user_id' => auth()->id(),
            'full_name' => $validated['nama_lengkap'],
            'employee_id' => $validated['nip'],
            'department' => $validated['departemen'],
            'item_name' => $validated['nama_barang'],
            'quantity' => $validated['jumlah'],
            'estimated_price' => $cleanPrice,
            'reference_link' => $validated['link_referensi'],
            'item_description' => $validated['deskripsi_barang'],
            'purpose_title' => $validated['judul_pengadaan'],
            'start_date' => $validated['tanggal_mulai'],
            'end_date' => $validated['tanggal_selesai'],
            'procurement_description' => $validated['deskripsi_pengadaan'],
        ]);

        // 2. Buat proposal_id dan simpan
        $submission->proposal_id = 'B-' . $submission->id;
        $submission->save();

        SubmissionTimeline::create([
            'submission_id' => $submission->id,
            'submission_type' => 'procure',
            'status' => 'Pending',
            'notes' => 'Submission created by user.',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('submission')->with('success', 'Pengajuan pengadaan barang (ID: ' . $submission->proposal_id . ') berhasil dikirim!');
    }
}