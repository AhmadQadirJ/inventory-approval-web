<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SubmissionTimeline extends Model
{
    protected $fillable = ['submission_id', 'submission_type', 'status', 'notes', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
