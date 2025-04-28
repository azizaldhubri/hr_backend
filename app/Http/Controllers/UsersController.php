<?php

namespace App\Http\Controllers;
use App\Http\Requests\RegisterRequest;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Notification ; 
use App\Notifications\LeaveRequestNotification; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\LeaveBalance;
use App\Models\LeaveType;


class UsersController extends Controller
{    
    public function index()
{
    return auth()->user()->notifications;
}
     public function getAllUsers(Request $request)
        {
            // $users = User::all(); // جلب جميع المستخدمين
            // return response()->json($users); // إرجاعهم كاستجابة JSON

            // $users=User::all();

            
        //     $employees = User::with('department')->get();
        //     $data=[
        //         'status'=>200 ,
        //         'data'=>$employees    
        //     ];
        //     return response()->json($data,200) ; 

        //     $employees = User::with('department')->get();
        //    return response()->json($employees);   

        //================

        $limit = $request->input('limit', 10); 
        $page = $request->input('page', 1);

  
        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        
  

    $employees = User::with('department')->paginate($limit);

    // $data = [
    //     'status' => 200,
    //     'data' => $employees
    // ];

    return response()->json([
        'data' => $employees,
        'pagination' => [
            'current_page' => $employees->currentPage(),
            'last_page' => $employees->lastPage(),
            'per_page' => $employees->perPage(),
            'total' => $employees->total(),
        ]
    ]);



    // return response()->json($data, 200);
    }

     public function getAllUserspignate(Request $request)
        {
            $users=User::all()->paginate($request->input('limit', 100))  ;
        return ($users) ;
           
       }
    public function authUser()
    {
            return Auth::user();
    }
    
    public function getUser($id)
    {
        // return User::findOrFail($id);
        // return User::where('id',$id)->get();
        

        // $user=User::where('id',$id)->get();
        // $data=[
        //     'status'=>200 ,
        //     'data'=>$user    
        // ];
        // return response()->json($user) ; 

        $employee = User::with('department:id,department_name')
        ->where('id', $id)         
        ->get();
         $data=[
            'status'=>200 ,
            'data'=>$employee    
        ];

    return response()->json($employee);
    }


    public function addUser(Request $request)
    {
        $request->validate([
            'name' => 'required',            
            'email' => 'required|email|unique:users',
            'salary' => 'required',
            'password' => 'required|min:6',
            'phone_number' => 'required',            
            'role' => 'required',
            'role_id' => 'required',
        ]);

        $filePaths = [];
        if($request->hasFile('files')) {
            
            foreach ($request->file('files') as $file) {             
               
                // $fileName= date('YmdHis') . '.' . $file->getClientOriginalExtension();
                
                $timestamp = Carbon::now()->format('YmdHis');
                $originalName = $file->getClientOriginalName();
                $newFileName = $timestamp . '-' . $originalName;               
                
                $filepath = $file->storeAs('assets', $newFileName, 'public');
                $filePaths[] = $filepath;            
               
            }
        }
               // حفظ البيانات مع مسارات الملفات كـ JSON في قاعدة البيانات
               $newEmployee = User::create([
                'name' => $request->input('name'),          
                'email' => $request->input('email'),
                'salary' => $request->input('salary'),
                'phone_number' => $request->input('phone_number'),
                'national_id' => $request->input('national_id'),
                'job_title' => $request->input('job_title'),
                'birth_date' => $request->input('birth_date'),
                'hire_date' => $request->input('hire_date'),
                'nationality' => $request->input('nationality'),
                'department_id' => $request->input('department_id'),
                'gender' => $request->input('gender'),
                'employment_type' => $request->input('employment_type'),
                'password' => Hash::make($request->password),
                'status' => $request->input('status'),           
                'role' => $request->input('role'),
                'role_id' => $request->input('role_id'),                 
                'file_paths' => json_encode($filePaths), // حفظ المسارات كـ JSON في حقل واحد
            ]);
            $this->assignLeaveBalance($newEmployee->id);
            Notification::create([
                'employee_id' => $request->input('admin'), // ID المشرف أو المدير (يمكنك تغييره وفقًا للنظام لديك)
                'message' => 'تم اضافة موظف جديد' ,
                'link_notification'=>$request->input('link_notification'),
            ]);
    
            return response()->json(['message' => 'Files uploaded successfully'], 200);
    }


    
    public function destroy($id)
    {           
        return  User::findOrFail($id)->delete();
    }

    public function editUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        
        $filePaths = [];
        if($request->hasFile('files')) {
            
            foreach ($request->file('files') as $file) {             
               
                // $fileName= date('YmdHis') . '.' . $file->getClientOriginalExtension();
                
                $timestamp = Carbon::now()->format('YmdHis');
                $originalName = $file->getClientOriginalName();
                $newFileName = $timestamp . '-' . $originalName;               
                
                $filepath = $file->storeAs('assets', $newFileName, 'public');
                $filePaths[] = $filepath;            
               
            }
        }
        $request->validate([
            'name' => 'required',            
            'email' => 'required',
            'salary' => 'required',
            'password' => 'required|min:6',
            'phone_number' => 'required',            
            'role' => 'required',
            'role_id' => 'required',
            
        ]);
        
        $user->update([
        'name'  => $request->name ,                  
        'email' => $request->email ,
        'salary' => $request->salary ,
        'phone_number' => $request->phone_number ,
        'national_id' => $request->national_id ,
        'job_title' => $request->job_title ,
        'birth_date' => $request->birth_date ,
        'hire_date' => $request->hire_date,
        'nationality' => $request-> nationality,
        'department_id' => $request->department_id,
        'gender' => $request->gender,
        'employment_type' => $request->employment_type,
        'password' => Hash::make($request->password),
        'status' => $request->status,           
        'role' => $request->role,
        'role_id' => $request->role_id ,
        'file_paths' => json_encode($filePaths),
        ]) ;    
        $user->save();
    }

    // j------------------
    public function assignLeaveBalance($employee_id)
{
    $leaveTypes = LeaveType::all();
    foreach ($leaveTypes as $type) {
        LeaveBalance::create([
            'employee_id' => $employee_id,
            'leave_type_id' => $type->id,
            'total_days' => $type->max_days,
            'used_days' => 0,
            'remaining_days' => $type->max_days
        ]);
    }
}
 

 
 


}
