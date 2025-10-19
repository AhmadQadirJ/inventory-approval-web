<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LendSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id', 'user_id', 'full_name', 'employee_id', 'branch', 'department', 'inventory_id',
        'quantity', 'purpose_title', 'start_date', 'end_date', 
        'start_time', 'end_time', 'description', 'status',
        'approved_by',
        'final_approver_nip', 'final_approver_name', 'final_approver_ttd_path',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); 
    }

    public function timelines()
    {
        return $this->hasMany(SubmissionTimeline::class, 'submission_id')->where('submission_type', 'lend')->latest();
    }
}