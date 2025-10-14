<?php
namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\LendSubmission; // Untuk mengambil data reservasi
use App\Models\User; // Asumsi relasi user ada
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Routing\Controller;

class ReservationController extends Controller
{
    public function index(Inventory $inventory, Request $request)
    {
        // --- 1. SETTING TANGGAL & KALENDER ---
        $selectedDate = $request->input('date') 
            ? Carbon::parse($request->input('date')) 
            : Carbon::today();

        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        // --- 2. LOGIKA HISTORY RESERVASI (Untuk Tabel History) ---
        
        // Ambil semua reservasi 'Accepted' atau 'Pending' untuk item ini
        $activeSubmissions = LendSubmission::where('inventory_id', $inventory->id)
            ->whereIn('status', ['Accepted', 'Pending'])
            ->with('user') // Eager load relasi User
            ->whereDate('end_date', '>=', Carbon::today()) 
            ->orderBy('start_date')
            ->get();

        $reservationHistory = $activeSubmissions->map(function ($submission) {
            return [
                'proposal_id' => $submission->proposal_id,
                'user_name' => $submission->user->name ?? 'User Unknown',
                'quantity' => $submission->quantity,
                'period' => Carbon::parse($submission->start_date)->format('d/m') . ' - ' . Carbon::parse($submission->end_date)->format('d/m/Y'),
                'time' => $submission->start_time . ' - ' . $submission->end_time,
                'status' => $submission->status
            ];
        });

        // --- 3. LOGIKA SLOT WAKTU (Untuk List Ketersediaan) ---
        
        $availableSlots = [];
        $startTime = Carbon::createFromTimeString('08:00:00');
        $endTime = Carbon::createFromTimeString('17:00:00');
        $currentSlot = $startTime->copy();

        while ($currentSlot->lessThan($endTime)) {
            $slotStart = $currentSlot->copy();
            $slotEnd = $currentSlot->copy()->addHour();
            $totalStock = $inventory->qty;
            
            // Hitung unit yang dipinjam pada slot ini
            $stockBooked = $activeSubmissions->filter(function ($sub) use ($selectedDate, $slotStart, $slotEnd) {
                // Cek apakah tanggal reservasi mencakup selectedDate
                $dateOverlap = $selectedDate->between(Carbon::parse($sub->start_date), Carbon::parse($sub->end_date));
                
                // Cek apakah jam reservasi mencakup slot waktu ini
                $timeOverlap = ($sub->start_time < $slotEnd->format('H:i:s')) && ($sub->end_time > $slotStart->format('H:i:s'));
                
                return $dateOverlap && $timeOverlap;
            })->sum('quantity');

            $availableSlots[] = [
                'time_start' => $slotStart->format('H:i'),
                'time_end' => $slotEnd->format('H:i'),
                'is_available' => ($totalStock - $stockBooked) > 0,
                'available_count' => $totalStock - $stockBooked,
            ];

            $currentSlot->addHour();
        }


        return view('inventory.reservation_index', compact(
            'inventory', 
            'selectedDate',
            'startOfMonth',
            'endOfMonth',
            'availableSlots', 
            'reservationHistory' // Kirim History untuk ditampilkan
        ));
    }
}