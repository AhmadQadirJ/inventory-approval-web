<?php

namespace App\Http\Controllers;

use App\Models\LendSubmission;
use App\Models\ProcureSubmission;
use App\Models\SubmissionTimeline;
use App\Models\Inventory;   
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Validation\Rule;

class SubmissionController extends Controller
{
    protected $validBranches = ['Bandung', 'Jakarta', 'Surabaya', 'Pusat'];
    protected $validDepartments = [
        'Operational', 'Human Resources', 'Finance and Acc Tax', 'Technology', 'Marketing & Creative',
        'General Affair', 'CHRD', 'COO'
    ];

    public function index()
    {
        return view('submission.index');
    }

    public function createLend()
    {
        return view('submission.lend-form');
    }

    public function createProcure()
    {
        return view('submission.procure-form');
    }

    public function storeLend(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap'          => 'required|string|max:255',
            'nip'                   => 'required|string|max:255',
            'branch'                => ['required', Rule::in($this->validBranches)],
            'departemen'            => ['required', Rule::in($this->validDepartments)],
            'inventory_id'          => 'required|exists:inventories,id',
            'quantity'              => 'required|integer|min:1',
            'judul_peminjaman'      => 'required|string|max:255',
            'tanggal_mulai'         => 'required|date',
            'start_time'            => 'required',
            'tanggal_selesai'       => 'required|date|after_or_equal:tanggal_mulai',
            'end_time'              => 'required',
            'deskripsi_peminjaman'  => 'required|string|max:500',
        ]);

        $item = Inventory::findOrFail($validated['inventory_id']);

        // Cek Stok Awal
        if ($item->qty < $validated['quantity']) {
            return back()->withInput()->with('error', 'Stok barang tidak mencukupi untuk jumlah yang Anda pinjam.');
        }

        $requestedPeriod = CarbonPeriod::create($validated['tanggal_mulai'], $validated['tanggal_selesai']);
        $requestedStartTime = Carbon::parse($validated['start_time']);
        $requestedEndTime = Carbon::parse($validated['end_time']);

        $conflictingBookings = LendSubmission::where('inventory_id', $item->id)
            ->where('status', 'like', 'Accepted%')
            ->where(function ($query) use ($validated) {
                $query->whereDate('start_date', '<=', $validated['tanggal_selesai'])
                      ->whereDate('end_date', '>=', $validated['tanggal_mulai']);
            })
            ->get();

        foreach ($requestedPeriod as $date) {
            $checkSlots = CarbonPeriod::create($requestedStartTime, '30 minutes', $requestedEndTime->copy()->subMinute()); 
            foreach ($checkSlots as $slotStart) {
                $slotEnd = $slotStart->copy()->addMinutes(30);
                $bookedQuantityOnSlot = 0; 

                foreach ($conflictingBookings as $sub) {
                    $subPeriod = CarbonPeriod::create($sub->start_date, $sub->end_date);
                    $subStartTime = Carbon::parse($sub->start_time);
                    $subEndTime = Carbon::parse($sub->end_time);

                    if ($subPeriod->contains($date) && $subStartTime->lt($slotEnd) && $subEndTime->gt($slotStart)) {
                        $bookedQuantityOnSlot += $sub->quantity;
                    }
                }

                $availableStock = $item->qty - $bookedQuantityOnSlot;
                if ($availableStock < $validated['quantity']) {
                    return back()->withInput()->with('error', 
                        'Stok tidak tersedia pada ' . $date->format('d/m/Y') . 
                        ' jam ' . $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i') .
                        '. Sisa unit tersedia pada slot tersebut: ' . $availableStock
                    );
                }
            }
        }

        // Buat record submission
        try {
        DB::transaction(function () use ($validated) {
            
            // Buat Lend Submission
            $submission = LendSubmission::create([
                'user_id'       => auth()->id(),
                'full_name'     => $validated['nama_lengkap'],
                'employee_id'   => $validated['nip'],
                'branch'        => $validated['branch'],
                'department'    => $validated['departemen'],
                'inventory_id'  => $validated['inventory_id'],
                'quantity'      => $validated['quantity'],
                'purpose_title' => $validated['judul_peminjaman'],
                'start_date'    => $validated['tanggal_mulai'],
                'end_date'      => $validated['tanggal_selesai'],
                'start_time'    => $validated['start_time'],
                'end_time'      => $validated['end_time'],
                'description'   => $validated['deskripsi_peminjaman'],
                'status'        => 'Pending',
            ]);

            // Simpan Proposal ID
            $submission->proposal_id = 'A-' . $submission->id;
            $submission->save();

            // Buat Timeline
            SubmissionTimeline::create([
                'submission_id'   => $submission->id,
                'submission_type' => 'lend',
                'status'          => 'Pending',
                'notes'           => 'Submission created by user.',
                'user_id'         => auth()->id(),
            ]);

        });
    
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Pengajuan gagal disimpan karena masalah database.');
        }

        return redirect()->route('submission.index')->with('success', 'Pengajuan peminjaman barang berhasil dikirim!');
    }

    public function storeProcure(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nip' => 'required|string|max:255',
            'branch' => ['required', Rule::in($this->validBranches)],
            'departemen' => ['required', Rule::in($this->validDepartments)],
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'estimasi_harga' => 'required|min:0',
            'link_referensi' => 'nullable|url',
            'deskripsi_barang' => 'required|string|max:500',
            'judul_pengadaan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'deskripsi_pengadaan' => 'required|string|max:500',
        ]);

        $cleanPrice = str_replace('.', '', $request->estimasi_harga);
        $submission = ProcureSubmission::create([
            'user_id' => auth()->id(),
            'full_name' => $validated['nama_lengkap'],
            'employee_id' => $validated['nip'],
            'branch' => $validated['branch'],
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
            'status' => 'Pending',
        ]);

        $submission->proposal_id = 'B-' . $submission->id;
        $submission->save();

        SubmissionTimeline::create([
            'submission_id'   => $submission->id, 
            'submission_type' => 'procure',
            'status'          => 'Pending',
            'notes'           => 'Submission created by user.',
            'user_id'         => auth()->id(),
        ]);

        return redirect()->route('submission.index')->with('success', 'Pengajuan pengadaan barang (ID: ' . $submission->proposal_id . ') berhasil dikirim!');
    }
}