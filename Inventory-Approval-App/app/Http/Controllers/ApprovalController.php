<?php

namespace App\Http\Controllers;

use App\Models\LendSubmission;
use App\Models\ProcureSubmission;
use App\Models\SubmissionTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;


class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status_filter');
        $waitingOnly = $request->input('waiting');

        $lendSubmissionsQuery = LendSubmission::with('inventory');
        $procureSubmissionsQuery = ProcureSubmission::query();

        if ($search) {
            $lendSubmissionsQuery->where(function ($query) use ($search) {
                $query->where('proposal_id', 'like', "%{$search}%")
                    ->orWhere('purpose_title', 'like', "%{$search}%")
                    ->orWhere('branch', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereRaw("LOWER('Peminjaman') LIKE ?", ["%".strtolower($search)."%"])
                    ->orWhereHas('inventory', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    });
            });
            
            $procureSubmissionsQuery->where(function ($query) use ($search) {
                $query->where('proposal_id', 'like', "%{$search}%")
                    ->orWhere('item_name', 'like', "%{$search}%")
                    ->orWhere('purpose_title', 'like', "%{$search}%")
                    ->orWhere('branch', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereRaw("LOWER('Pengadaan') LIKE ?", ["%".strtolower($search)."%"]);
            });
        }

        $lendSubmissions = $lendSubmissionsQuery->get();
        $procureSubmissions = $procureSubmissionsQuery->get();

        // Mapping data peminjaman
        $mappedLend = $lendSubmissions->map(fn($item) => (object) [
            'id' => $item->proposal_id,
            'type' => 'Peminjaman',
            'item' => $item->inventory?->nama,
            'purpose' => $item->purpose_title,
            'date' => $item->created_at->format('d/m/Y'),
            'status' => $item->status,
            'branch' => $item->branch,
        ]);

        // Mapping data pengadaan
        $mappedProcure = $procureSubmissions->map(fn($item) => (object) [
            'id' => $item->proposal_id,
            'type' => 'Pengadaan',
            'item' => $item->item_name,
            'purpose' => $item->purpose_title,
            'date' => $item->created_at->format('d/m/Y'),
            'status' => $item->status,
            'branch' => $item->branch,
        ]);

        $submissions = new \Illuminate\Support\Collection(array_merge($mappedLend->all(), $mappedProcure->all()));

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

        if ($statusFilter) {
            $submissions = $submissions->filter(function ($submission) use ($statusFilter) {
                return Str::contains($submission->status, $statusFilter);
            });
        }


        $submissions = $submissions->sortByDesc('date');

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
        if (auth()->user()->role !== 'General Affair') {
            abort(403, 'Unauthorized action.');
        }

        $submission = null;
        $submissionType = null;
        if (Str::startsWith($proposal_id, 'A-')) {
            $submission = LendSubmission::where('proposal_id', $proposal_id)->firstOrFail();
            $submissionType = 'lend';
        } elseif (Str::startsWith($proposal_id, 'B-')) {
            $submission = ProcureSubmission::where('proposal_id', $proposal_id)->firstOrFail();
            $submissionType = 'procure';
        }

        if ($submission && $submission->status === 'Pending') {
            SubmissionTimeline::create([
                'submission_id'   => $submission->id,
                'submission_type' => $submissionType,
                'status'          => 'Pending',
                'notes'           => 'Submission has been received and is being processed by General Affair.',
                'user_id'         => auth()->id(),
            ]);

            $submission->status = 'Processed - GA';
            $submission->save();

            return redirect()->route('approval.index')->with('success', 'Proposal ' . $proposal_id . ' is now being processed.');
        }

        return redirect()->route('approval.index')->with('error', 'Invalid action or proposal not found.');
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

        $submission->load('timelines.user');

        $submission->type = $type;

        return view('history.show', [
            'submission' => $submission
        ]);
    }

    public function approve(Request $request, $proposal_id)
    {
        $submission = null;
        $submissionType = null;
        if (Str::startsWith($proposal_id, 'A-')) {
            $submission = LendSubmission::where('proposal_id', $proposal_id)->firstOrFail();
            $submissionType = 'lend';
        } elseif (Str::startsWith($proposal_id, 'B-')) {
            $submission = ProcureSubmission::where('proposal_id', $proposal_id)->firstOrFail();
            $submissionType = 'procure';
        }

        $approver = Auth::user();
        $userRole = $approver->role;
        $currentStatus = $submission->status;
        $nextStatus = $currentStatus;

        if ($submissionType === 'lend') {
            if ($userRole === 'General Affair' && $currentStatus === 'Processed - GA') {
                $nextStatus = 'Processed - COO/CHRD';
            } elseif (in_array($userRole, ['COO', 'CHRD']) && $currentStatus === 'Processed - COO/CHRD') {
                $nextStatus = 'Accepted - ' . $userRole;
            }
        } 
        elseif ($submissionType === 'procure') {
            if ($userRole === 'General Affair' && $currentStatus === 'Processed - GA') {
                $nextStatus = 'Processed - Finance';
            } elseif ($userRole === 'Finance' && $currentStatus === 'Processed - Finance') {
                $nextStatus = 'Processed - CHRD';
            } elseif ($userRole === 'CHRD' && $currentStatus === 'Processed - CHRD') {
                $nextStatus = 'Accepted - CHRD';
            }
        }

        if ($nextStatus !== $currentStatus) {

            if (Str::startsWith($nextStatus, 'Accepted') && $submissionType === 'lend') {
                $item = $submission->inventory;
                $requestedQuantity = $submission->quantity;
                $requestedPeriod = CarbonPeriod::create($submission->start_date, $submission->end_date);
                $requestedStartTime = Carbon::parse($submission->start_time);
                $requestedEndTime = Carbon::parse($submission->end_time);

                $conflictingBookings = LendSubmission::where('inventory_id', $item->id)
                    ->where('status', 'like', 'Accepted%')
                    ->where('id', '!=', $submission->id)
                    ->where(function ($query) use ($submission) {
                        $query->whereDate('start_date', '<=', $submission->end_date)
                            ->whereDate('end_date', '>=', $submission->start_date);
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
                        if ($availableStock < $requestedQuantity) {
                            return back()->with('error', 
                                'Approval Gagal: Stok tidak tersedia pada ' . $date->format('d/m/Y') . 
                                ' jam ' . $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i') .
                                '. Sisa unit tersedia pada slot tersebut: ' . $availableStock
                            );
                        }
                    }
                }
            }

            SubmissionTimeline::create([
                'submission_id'   => $submission->id,
                'submission_type' => $submissionType,
                'status'          => $currentStatus,
                'notes'           => $request->notes,
                'user_id'         => $approver->id,
            ]);

            if (Str::startsWith($nextStatus, 'Accepted')) {
                $submission->final_approver_name = $approver->name;
                $submission->final_approver_nip = $approver->nip ?? 'N/A';

                SubmissionTimeline::create([
                    'submission_id'   => $submission->id,
                    'submission_type' => $submissionType,
                    'status'          => $nextStatus,
                    'notes'           => 'Proposal has been fully approved.',
                    'user_id'         => $approver->id,
                ]);
            }

            $submission->status = $nextStatus;
            $submission->save();

            return redirect()->route('approval.index')->with('success', "Proposal $proposal_id approved.");
        }

        return redirect()->route('approval.index')->with('error', 'Invalid approval step.');
    }

    public function reject(Request $request, $proposal_id)
    {
        $submission = null;
        $submissionType = null;

        if (Str::startsWith($proposal_id, 'A-')) {
            $submission = LendSubmission::findOrFail($request->id);
            $submissionType = 'lend';
        } elseif (Str::startsWith($proposal_id, 'B-')) {
            $submission = ProcureSubmission::findOrFail($request->id);
            $submissionType = 'procure';
        } else {
            abort(404);
        }

        $userRole = Auth::user()->role;
        $currentStatus = $submission->status; 

        $newStatus = 'Rejected - ' . $userRole;

        $submission->status = $newStatus;
        $submission->save();

        SubmissionTimeline::create([
            'submission_id' => $submission->id,
            'submission_type' => $submissionType,
            'status' => $newStatus,
            'notes' => $request->notes ?? "Rejected by {$userRole}.",
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('approval.index')->with('success', "Proposal $proposal_id has been rejected by {$userRole}.");
    }

    public function printPdf($proposal_id)
    {
        $submission = null;
        $type = null;

        if (Str::startsWith($proposal_id, 'A-')) {
            $submission = LendSubmission::where('proposal_id', $proposal_id)->first();
            $type = 'Peminjaman';
        } elseif (Str::startsWith($proposal_id, 'B-')) {
            $submission = ProcureSubmission::where('proposal_id', $proposal_id)->first();
            $type = 'Pengadaan';
        }

        if (!$submission || !Str::startsWith($submission->status, 'Accepted')) {
            abort(403, 'Submission Not Accepted or Not Found.');
        }
        $submission->type = $type;

        if ($submission->approved_by) {
        } else {
           $statusParts = explode(' - ', $submission->status);
            $submission->approved_by = end($statusParts); 
        }

        $data = [
            'submission' => $submission,
            'document_number' => date('Y').'/INV/'.str_replace('-', '/', $submission->proposal_id)
        ];

        $pdf = Pdf::loadView('pdf.submission-document', $data);
        return $pdf->stream('submission-' . $submission->proposal_id . '.pdf');
    }

    public function printDetail($proposal_id)
    {
        $submission = null; $type = null;
        if (Str::startsWith($proposal_id, 'A-')) {
            $submission = LendSubmission::where('proposal_id', $proposal_id)->firstOrFail(); $type = 'Peminjaman';
        } elseif (Str::startsWith($proposal_id, 'B-')) {
            $submission = ProcureSubmission::where('proposal_id', $proposal_id)->firstOrFail(); $type = 'Pengadaan';
        } else { abort(404); }
        $submission->load('timelines.user');
        $submission->type = $type;

        $pdf = Pdf::loadView('history.print-detail', ['submission' => $submission]);

        return $pdf->stream('detail-' . $submission->proposal_id . '.pdf');
    }
}