<?php

namespace App\Http\Controllers;

use App\Models\LendSubmission;
use App\Models\ProcureSubmission;
use App\Models\SubmissionTimeline;
use App\Models\Inventory;   
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
        // Validasi disesuaikan dengan nama 'departemen' dari form
        $validated = $request->validate([
            'nama_lengkap'         => 'required|string|max:255',
            'nip'                  => 'required|string|max:255',
            'departemen'           => 'required|string|max:255', // Diubah di sini
            'inventory_id'         => 'required|exists:inventories,id',
            'quantity'             => 'required|integer|min:1',
            'judul_peminjaman'     => 'required|string|max:255',
            'tanggal_mulai'        => 'required|date',
            'start_time'           => 'required',
            'tanggal_selesai'      => 'required|date|after_or_equal:tanggal_mulai',
            'end_time'             => 'required',
            'deskripsi_peminjaman' => 'required|string|max:300',
        ]);

        $item = Inventory::findOrFail($validated['inventory_id']);

        if ($item->qty < $validated['quantity']) {
            return back()->withInput()->with('error', 'Stok barang tidak mencukupi untuk jumlah yang Anda pinjam.');
        }

        $submission = LendSubmission::create([
            'user_id'       => auth()->id(),
            'full_name'     => $validated['nama_lengkap'],
            'employee_id'   => $validated['nip'],
            'department'    => $validated['departemen'], // Diubah di sini
            'inventory_id'  => $validated['inventory_id'],
            'quantity'      => $validated['quantity'],
            'purpose_title' => $validated['judul_peminjaman'],
            'start_date'    => $validated['tanggal_mulai'],
            'end_date'      => $validated['tanggal_selesai'],
            'start_time'    => $validated['start_time'],
            'end_time'      => $validated['end_time'],
            'description'   => $validated['deskripsi_peminjaman'],
        ]);

        $submission->proposal_id = 'A-' . $submission->id;
        $submission->save();

        SubmissionTimeline::create([
            'submission_id'   => $submission->id,
            'submission_type' => 'lend',
            'status'          => 'Pending',
            'notes'           => 'Submission created by user.',
            'user_id'         => auth()->id(),
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