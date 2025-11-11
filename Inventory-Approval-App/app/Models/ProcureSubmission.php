<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcureSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'user_id', 'full_name', 'employee_id', 'branch', 'department', 'item_name',
        'quantity', 'estimated_price', 'reference_link', 'item_description',
        'purpose_title', 'start_date', 'end_date', 'procurement_description', 'status',
        'approved_by',
        'final_approver_nip', 'final_approver_name', 'final_approver_ttd_path',
    ];

    public function timelines()
    {
        return $this->hasMany(SubmissionTimeline::class, 'submission_id')->where('submission_type', 'procure')->latest();
    }
}