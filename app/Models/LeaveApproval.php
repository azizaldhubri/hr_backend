<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApproval extends Model
{
    use HasFactory;
    protected $fillable = [         
        'leave_request_id',
        'leave_requests',  
         'approved_by',  
        'status', 
        'comments',
    ];
    // protected $fillable = ['leave_request_id', 'approver_id', 'status', 'comment'];

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
