<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcureSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'full_name', 'employee_id', 'department', 'item_name',
        'quantity', 'estimated_price', 'reference_link', 'item_description',
        'purpose_title', 'start_date', 'end_date', 'procurement_description', 'status',
    ];
}