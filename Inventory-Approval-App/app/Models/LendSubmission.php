<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LendSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'full_name', 'employee_id', 'department', 'item_name',
        'quantity', 'purpose_title', 'start_date', 'end_date', 'description', 'status',
    ];
}