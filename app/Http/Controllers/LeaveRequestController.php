<?php

namespace App\Http\Controllers;
use App\Models\LeaveRequest ; 
use App\Models\User ; 
use App\Models\Notification ; 
use App\Notifications\LeaveRequestNotification;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
 
use App\Models\LeaveApproval;
use App\Models\LeaveBalance;

class LeaveRequestController extends Controller
{
    // public function index1(Request $request)
    // {
    //     // return LeaveRequest::with('employee', 'leaveType', 'approver')->get();
    //     return LeaveRequest::with('employee', 'leaveType', 'approver')->paginate($request->input('limit', 100));
    // }


    public function getRequestCounts(Request $request)
    {
        // return LeaveRequest::with('employee', 'leaveType', 'approver')->get();
       $pendingLeaves = LeaveRequest::where('status', 'pending')->count();
       return $pendingLeaves ; 
    }
    

    
    public function getLeaveCounts(Request $request)
    {
        // return LeaveRequest::with('employee', 'leaveType', 'approver')->get();
       $approvedLeaves = LeaveRequest::where('status', 'approved')
       ->whereDate('start_date', '<=', now()->addDay())        
        ->whereDate('end_date', '>=', now()->addDay())
       ->count();
       return $approvedLeaves ; 
    }


    public function getEmployeesOnLeave()
{   
    $employeesOnLeave = LeaveRequest::with([
        'employee:id,name',
         'leaveType'])
        ->where('status', 'approved')       
        ->whereDate('start_date', '<=', now()->addDay())        
        ->whereDate('end_date', '>=', now()->addDay())
        ->get();

    return response()->json($employeesOnLeave);
}

    public function store(Request $request)
    {     
        
        // $first_name=auth('api')->user()->first_name ;
        //  $last_name= auth('api')->user()->last_name;
         
        //  $name = $first_name . " ". $last_name;
         
         $name= auth('api')->user()->name;
        
        $userId = auth('api')->id();
        $leave_type=$request ->leave_type_id ;
        
        $date1 = Carbon::create($request->input('start_date'));
        $date2 = Carbon::create($request->input('end_date'));       
        $difference = $date1->diffInDays($date2)+1;            
       
        $leaveBalances = LeaveBalance::where('employee_id', $userId)
        ->where('leave_type_id', $leave_type)
        ->first();
            if (!$leaveBalances->remaining_days || $leaveBalances->remaining_days < $difference) {
                return response()->json(['message' => 'رصيد الإجازة غير كافٍ!'], 400);
            }


        // ------------------------------
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);
 

        $filePaths = [];
        if($request->hasFile('files')) {
            
            foreach ($request->file('files') as $file) {                   
                $timestamp = Carbon::now()->format('YmdHis');
                $originalName = $file->getClientOriginalName();
                $newFileName = $timestamp . '-' . $originalName;                   
                $filepath = $file->storeAs('assets', $newFileName, 'public');
                $filePaths[] = $filepath;            
               
            }
        }
            
        $leaveRequest= LeaveRequest::create([   
                'employee_id'  => $request->input('employee_id'),
                'leave_type_id' => $request->input('leave_type_id'),                  
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'total_days' => (new \Carbon\Carbon($request->end_date))->diffInDays(new \Carbon\Carbon($request->start_date)) + 1,
                // 'total_days' => $request->input('total_days'),
                'reason' => $request->input('reason'),
                'status' => $request->input('status'),
                'approved_by' => $request->input('approved_by'),                                      
                'file_paths' => json_encode($filePaths), // حفظ المسارات كـ JSON في حقل واحد
            ]);



        Notification::create([
                'employee_id' => $request->input('admin'), // ID المشرف أو المدير (يمكنك تغييره وفقًا للنظام لديك)
                'message' => 'لديك طلب إجازة جديد من '. " " .$name,
                'link_notification'=>$request->input('link_notification'),
            ]);
            // -----------------------------------------------------------
            return response()->json(['message' => 'Files uploaded successfully'], 200);
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);
        $leaveRequest->update(['status' => $request->status]);
        return response()->json(['message' => 'Status updated']);
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        $leaveRequest->delete();
        return response()->json(['message' => 'Request deleted']);
    }
    // -------------------

    // $postAttachments = !empty($record->file_paths) ? json_decode($record->file_paths, true) : [];
    public function index(Request $request)
{        
 
    $query = LeaveRequest::query();

    // نختار فقط الأعمدة المهمة
    $query->select(['id', 'employee_id', 'leave_type_id' ,'status','approved_by','reason','total_days','end_date','start_date', 'file_paths']);

    // نحمل العلاقات مع تحديد الحقول المطلوبة فقط
    $query->with([
        'employee:id,name',
        'leaveType:id,name',
        'approver:id,name',
    ]);
    if ($request->has('employee_id')) {
        $query->where('employee_id', $request->employee_id);
    }

    if ($request->has('status')) {
        $query->where('status', $request->status);
    }
    

    $leaveRequests = $query->latest()->paginate($request->input('limit', 100));

    $leaveRequests->getCollection()->transform(function ($leaveRequest) {
        $leaveRequest->file_paths = $leaveRequest->file_paths 
            ? json_decode($leaveRequest->file_paths, true) 
            : [];

        $leaveRequest->approver_name = $leaveRequest->approver ? $leaveRequest->approver->name : null;

        return $leaveRequest;
        }); 

        return response()->json($leaveRequests);
}

public function updateStatus(Request $request, $id)
    {   
        
        $name=auth('api')->user()->name ;
        // $last_name= auth('api')->user()->last_name;
       
        // $name = $fname . " ". $last_name;
        $userId = auth('api')->id();        
         
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'comments' => 'nullable|string'
        ]);

        $leaveRequest = LeaveRequest::findOrFail($id);

        
         
        if ($leaveRequest->status !== 'pending') {
            return response()->json(['message' => 'تمت معالجة الطلب بالفعل!'], 400);
        }

        // إذا كان الطلب "موافَق عليه"، قم بخصم الرصيد
        if ($request->status === 'approved') {
            $balance = LeaveBalance::where('employee_id', $leaveRequest->employee_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->first();
             // هذا الكود لا داعي لوجوده لانه قد تم التحقق من رصيد الاجازة عند  الطلب 
            if (!$balance || $balance->remaining_days < $leaveRequest->total_days) {
                return response()->json(['message' => 'رصيد الإجازة غير كافٍ!'], 400);
            }

            $balance->used_days += $leaveRequest->total_days;
            $balance->remaining_days -= $leaveRequest->total_days;
            $balance->save();
        }

        // تحديث حالة الطلب
        $leaveRequest->update(['status' => $request->status]);
        $leaveRequest->update(['approved_by' =>$userId]);

        // إدخال سجل الموافقة/الرفض
        LeaveApproval::create([
            'leave_request_id' => $leaveRequest->id,
            'approved_by' =>  $userId,
            'status' => $request->status,
            'comments' => $request->comments
        ]);
        Notification::create([
            'employee_id' => $leaveRequest->employee_id, // ID المشرف أو المدير (يمكنك تغييره وفقًا للنظام لديك)
            'message' => ' لقد '.' '. $request->comments. " " .'من'.' '.$name,
            'link_notification'=>$request->input('link_notification'),
        ]);
        


        return response()->json(['message' => 'تم تحديث حالة الطلب بنجاح!']);
    }

}
