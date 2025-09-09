<?php

namespace App\Http\Controllers;

use App\Models\LendSubmission;
use App\Models\ProcureSubmission;
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
            'deskripsi_peminjaman' => 'required|string',
        ]);

        LendSubmission::create([
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

        return redirect()->route('submission')->with('success', 'Pengajuan peminjaman barang berhasil dikirim!');
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
            'estimasi_harga' => 'required|numeric|min:0',
            'link_referensi' => 'nullable|url',
            'deskripsi_barang' => 'required|string',
            'judul_pengadaan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'deskripsi_pengadaan' => 'required|string',
        ]);

        ProcureSubmission::create([
            'user_id' => auth()->id(),
            'full_name' => $validated['nama_lengkap'],
            'employee_id' => $validated['nip'],
            'department' => $validated['departemen'],
            'item_name' => $validated['nama_barang'],
            'quantity' => $validated['jumlah'],
            'estimated_price' => $validated['estimasi_harga'],
            'reference_link' => $validated['link_referensi'],
            'item_description' => $validated['deskripsi_barang'],
            'purpose_title' => $validated['judul_pengadaan'],
            'start_date' => $validated['tanggal_mulai'],
            'end_date' => $validated['tanggal_selesai'],
            'procurement_description' => $validated['deskripsi_pengadaan'],
        ]);

        return redirect()->route('submission')->with('success', 'Pengajuan pengadaan barang berhasil dikirim!');
    }
}