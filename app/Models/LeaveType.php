<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class LeaveType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'max_days', 'carry_forward', 
        
    ];
     

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($leaveType) {
            if ($leaveType->isDirty('max_days')) {
                self::updateLeaveBalances($leaveType);
            }
        });
    }

    public static function updateLeaveBalances($leaveType)
    {               
        DB::table('leave_balances')
            ->where('leave_type_id', $leaveType->id)
            ->update(['total_days' => $leaveType->max_days]);
    }

    
    
}
