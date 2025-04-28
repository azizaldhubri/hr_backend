<?php

namespace App\Http\Controllers;
use App\Models\Department; 
use App\Models\User; 
use Illuminate\Http\Request;
class DepartmentContoller extends Controller
// class DepartmentController extends Controller
{
    public function getAllDepartment()
    {
        $department = Department::all(); // جلب جميع الصفحات
        return response()->json($department);
    }

    public function getDepartment($id)
    {
        $department = Department::findOrFail($id); // جلب جميع الصفحات
        return response()->json($department);

        // $department=Department::where('id',$id)->get();
        // $data=[
        //     'status'=>200 ,
        //     'data'=>$department    
        // ];
        // return response()->json($department) ;
    }
    
    public function store(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $request->validate([
            'department_name' => 'required',
            'responsible_manager' => 'required',            
            'location' => 'required',         
            
        ]);

        // إنشاء دور جديد باستخدام البيانات
        $department = Department::create([                        
            'department_name' => $request->input('department_name'),
            'responsible_manager' => $request->input('responsible_manager'),
            'description' => $request->input('description'),
            'location' => $request->input('location'),
            'creation_date' => $request->input('creation_date'),
            'Status' => $request->input('Status'),
                    
        ]);

        // إرسال استجابة عند النجاح
        return response()->json([
            'message' => 'تم إضافة القسم بنجاح',
            'department' => $department
        ], 201);
    }

    public function editDepartment(Request $request, $id)
    {   $department = Department::findOrFail($id);
        // التحقق من صحة البيانات المدخلة
        $request->validate([
            'department_name' => 'required',
            'responsible_manager' => 'required',            
            'location' => 'required',         
            
        ]);

        // إنشاء دور جديد باستخدام البيانات
        $department->update([             
            'department_name'  => $request->department_name ,
            'responsible_manager' => $request->responsible_manager,          
            'description' => $request->description ,
            'location' => $request->location ,
            'creation_date' => $request->creation_date ,
            'Status' => $request->Status ,                    
        ]);

        // إرسال استجابة عند النجاح
        return response()->json([
            'message' => 'تم إضافة القسم بنجاح',
            'department' => $department
        ], 201);
    }

      
    public function destroy($id)
    {           
        return  Department::findOrFail($id)->delete();
    }

    public function getCounts()
{
    $departmentsCount = Department::count();
    $employeesCount = User::count();

    return response()->json([
        'departments' => $departmentsCount,
        'employees' => $employeesCount,
    ]);
}
}
