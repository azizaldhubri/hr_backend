<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    use HasFactory;
    protected $fillable = [  
        'employee_id', 'leave_type_id',  
        'total_days', 'used_days', 'remaining_days',  
    ];
    
    public function employee()    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // العلاقة مع نوع الإجازة
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
    
}
