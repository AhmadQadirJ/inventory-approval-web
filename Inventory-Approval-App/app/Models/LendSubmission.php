<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LendSubmission extends Model
{
    use HasFactory;

    // Ganti 'item_name' dengan 'inventory_id'
    protected $fillable = [
        'proposal_id', 'user_id', 'full_name', 'employee_id', 'department', 'inventory_id',
        'quantity', 'purpose_title', 'start_date', 'end_date', 'description', 'status',
    ];

    // Tambahkan relasi ini
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function timelines()
    {
        return $this->hasMany(SubmissionTimeline::class, 'submission_id')->where('submission_type', 'lend')->latest();
    }
}