<?php

namespace App\Http\Controllers;

use App\Models\LendSubmission;
use App\Models\ProcureSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Ambil data Peminjaman (Lend)
        $lendSubmissions = LendSubmission::with('inventory')->where('user_id', $userId)->get();
        
        // Ambil data Pengadaan (Procure)
        $procureSubmissions = ProcureSubmission::where('user_id', $userId)->get();

        // Gabungkan semua data
        $allSubmissions = collect($lendSubmissions->map(function ($item) {
            return (object) [
                'id' => $item->proposal_id,
                'type' => 'Peminjaman',
                'item' => $item->inventory?->nama,
                'purpose' => $item->purpose_title,
                'date' => $item->created_at,
                'status' => $item->status,
            ];
        }))->merge(collect($procureSubmissions->map(function ($item) {
            return (object) [
                'id' => $item->proposal_id,
                'type' => 'Pembelian',
                'item' => $item->item_name,
                'purpose' => $item->purpose_title,
                'date' => $item->created_at,
                'status' => $item->status,
            ];
        })));


        // Ambil data terbaru
        $latestSubmissions = $allSubmissions->sortByDesc('date')->take(5)->map(function($item) {
            $item->date = $item->date->format('d/m/Y');
            return $item;
        });

        // Hitung statistik untuk cards
        $pendingCount = $allSubmissions->filter(fn($item) => Str::startsWith($item->status, 'Pending'))->count();

        $acceptedCount = $allSubmissions->filter(fn($item) =>
            Str::startsWith($item->status, 'Accepted')
        )->count();

        $rejectedCount = $allSubmissions->filter(fn($item) =>
            Str::startsWith($item->status, 'Rejected')
        )->count();

        $processedCount = $allSubmissions->filter(fn($item) =>
            Str::startsWith($item->status, 'Processed')
        )->count();

        // Kirim semua data ke view
        return view('dashboard', [
            'latestSubmissions' => $latestSubmissions,
            'pendingCount' => $pendingCount,
            'acceptedCount' => $acceptedCount,
            'rejectedCount' => $rejectedCount,
            'processedCount' => $processedCount,
        ]);
    }
}