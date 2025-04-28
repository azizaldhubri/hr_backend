<?php

namespace App\Http\Controllers;
use App\Models\LeaveApproval;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Http\Request;

class LeaveBalanceController extends Controller
{
    public function getAllLeaveBalances()
    {
        $leaveBalances = LeaveBalance::with(['employee', 'leaveType'])->get();

        return response()->json($leaveBalances);
    }
}
