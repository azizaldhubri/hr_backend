<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id', 'leave_type_id', 'start_date', 'end_date', 
        'total_days', 'reason', 'status', 'approved_by','file_paths'
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class );
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
 
}
