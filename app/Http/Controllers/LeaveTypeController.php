<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveType ; 
use App\Models\User ; 
use App\Models\LeaveBalance;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
 
 

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leaveType = LeaveType::all();
        return response()->json($leaveType);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([             
            'name' => 'required',             
            'max_days' => 'required',      
            
        ]);
        // حفظ البيانات مع مسارات الملفات كـ JSON في قاعدة البيانات
        $leaveType =LeaveType::create([   
            'name'  => $request->input('name'),
            'max_days' => $request->input('max_days'),                  
            'carry_forward' => $request->input('carry_forward'),                                            
            
        ]);
        
        $employees = User::all();
        foreach ($employees as $employee) {
            LeaveBalance::create([
                'employee_id' => $employee->id,
                'leave_type_id' => $leaveType->id,
                'total_days' => $request->max_days,
                'used_days' => 0,
                'remaining_days' => $request->max_days,
            ]);}
    
            return response()->json(['message' => 'FleavesType   created successfully'], 200);
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        // $request->validate(['status' => 'required|in:approved,rejected']);
        // $leaveRequest->update(['status' => $request->status]);
        // return response()->json(['message' => 'Status updated']);
    }


    public function getLeavesType($id)
    {
        $leavesType=LeaveType::findOrFail($id);
        return $leavesType;

    }

    public function update1(Request $request, $id)
    {  
            $leaveType = LeaveType::findOrFail($id);
            $oldMaxDays = $leaveType->max_days; // القيمة القديمة
        
            $leaveType->update([
                'name' => $request->name,
                'max_days' => $request->max_days,
            ]);
        
            $newMaxDays = $request->max_days; // القيمة الجديدة
        
            // تحديث أرصدة جميع الموظفين الذين لديهم هذا النوع من الإجازة
            LeaveBalance::where('leave_type_id', $id)->get()->each(function ($Item) use ($oldMaxDays, $newMaxDays) {
                $usedDays = $Item->used_days; // الأيام المستخدمة من قبل الموظف
                $remainingDays = $Item->remaining_days; // الأيام المتبقية الحالية        
               
                $remaining_daysOld=$Item->used_days ; 
                $Newremaining_days= $newMaxDays - $remaining_daysOld ;              
                // تحديث الأيام المتبقية مع الحفاظ على الأيام المستخدمة كما هي
                $Item->update([
                    'total_days' => $newMaxDays,
                    'remaining_days' => max(0, $Newremaining_days),
                ]);
            });
        
            return response()->json(['message' => 'تم تحديث نوع الإجازة وأرصدة الموظفين بنجاح']);
    }

    public function destroy($id)
    {           
        return  LeaveType::findOrFail($id)->delete();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }
 

  
   
    
}
