<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\LendSubmission;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Routing\Controller;

class ReservationController extends Controller
{
    public function index(Inventory $inventory, Request $request)
    {
        $selectedDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();
        $activeOnlyToday = $request->input('active_today'); // Ambil status checkbox

        // Data untuk kalender
        $startOfMonth = $selectedDate->copy()->startOfMonth();
        $endOfMonth = $selectedDate->copy()->endOfMonth();
        $dateRange = CarbonPeriod::create($startOfMonth, $endOfMonth);

        $query = LendSubmission::where('inventory_id', $inventory->id)
            ->where('status', 'like', 'Accepted%')
            ->with('user')
            ->orderBy('start_date');

        if ($activeOnlyToday) {
            $query->whereDate('start_date', '<=', $selectedDate)
                  ->whereDate('end_date', '>=', $selectedDate);
        }
        
        $activeSubmissions = $query->get();
            
        $reservationHistory = $activeSubmissions->map(function ($submission) {
            return [
                'proposal_id'   => $submission->proposal_id,
                'user_name'     => $submission->full_name,
                'branch'        => $submission->branch,
                'department'    => $submission->department,
                'purpose_title' => $submission->purpose_title,
                'period'        => Carbon::parse($submission->start_date)->format('d/m/Y') . ' - ' . Carbon::parse($submission->end_date)->format('d/m/Y'),
                'time'          => \Carbon\Carbon::parse($submission->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($submission->end_time)->format('H:i'),
                'status'        => $submission->status
            ];
        });

        $availableSlots = [];
        $startTime = Carbon::createFromTimeString('07:00:00');
        $endTime = Carbon::createFromTimeString('22:00:00');
        $timeIntervals = CarbonPeriod::create($startTime, '30 minutes', $endTime);

        foreach ($timeIntervals as $slotStart) {
            if ($slotStart->eq($endTime)) continue;

            $slotEnd = $slotStart->copy()->addMinutes(30);
            $totalStock = $inventory->qty;

            $conflictingSubmissions = $activeSubmissions->filter(function ($sub) use ($selectedDate, $slotStart, $slotEnd) {
                $submissionPeriod = CarbonPeriod::create($sub->start_date, $sub->end_date);
                $timeOverlap = (Carbon::parse($sub->start_time)->lt($slotEnd)) && (Carbon::parse($sub->end_time)->gt($slotStart));
                return $submissionPeriod->contains($selectedDate) && $timeOverlap;
            });
            
            $stockBooked = $conflictingSubmissions->sum('quantity');
            $isBooked = $conflictingSubmissions->isNotEmpty();

            $availableSlots[] = [
                'time_start'      => $slotStart->format('H:i'),
                'is_booked'       => $isBooked,
                'available_count' => $totalStock - $stockBooked,
            ];
        }

        return view('inventory.reservation_index', compact(
            'inventory', 
            'selectedDate',
            'dateRange',
            'availableSlots', 
            'reservationHistory'
        ));
    }
}