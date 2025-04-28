<?php

namespace App\Http\Controllers;
use App\Models\Payroll;
use App\Models\User; 
use App\Models\Deductions;
use App\Models\Absences;
use App\Models\Allowances;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\LeaveRequest ; 
class PayrollController extends Controller
{
    public function getPayrolls(Request $request)
    {   
       
        // $payrolls = Payroll::with('employee')->get();
        // $users = User::paginate($request->input('limit', 100));
        $payrolls = Payroll::with('employee')->paginate($request->input('limit', 100));
        return response()->json($payrolls);
    }

    public function addDeduction(Request $request)
    { 
        // return $request ;
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'deduction_type' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        Deductions::create($request->all());
        return response()->json(['message' => 'تمت إضافة الخصم بنجاح']);
    }

    public function addAbsence(Request $request)
    {

        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'absence_date' => 'required|date',
            'absence_type' => 'required|string',
            'deduction_amount' => 'required|numeric',
        ]);

        // ---------------------------------------------------------------
        $isOnLeave = LeaveRequest::where('employee_id', $request->employee_id)
        ->where('status', 'approved')
        ->whereDate('start_date', '<=', $request->absence_date)
        ->whereDate('end_date', '>=', $request->absence_date)
        ->exists();
        if ($isOnLeave) {
            return response()->json(['message' => 'لا يمكن تسجيل الغياب، الموظف في إجازة'], 400);
        }
        // =================================================================

        Absences::create($request->all());
        return response()->json(['message' => 'تمت إضافة الغياب بنجاح']);
    }

    // ------------------------
    public function addallowances(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'effective_date' => 'required|date',
            'allowance_type' => 'required|string',
            'amount' => 'required|numeric',
        ]);       

        Allowances::create($request->all());
        return response()->json(['message' => 'تمت إلاضافة بنجاح']);
    }


    // ------------------------------

    public function calculateSalary(Request $request)
    {
        $employeeId = $request->employee_id;
        $month = $request->month;
        $year = $request->year;

        $payroll = Payroll::calculateSalary($employeeId, $month, $year);
         
        return response()->json($payroll);
    }



    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'month' => 'required|integer',
            'year' => 'required|integer',
            'basic_salary' => 'required|numeric',
            'total_allowances' => 'nullable|numeric',
            'total_deductions' => 'nullable|numeric',
            'net_salary' => 'required|numeric',
            'payment_status' => 'required|in:paid,unpaid',
        ]);

        $payroll = Payroll::create($request->all());

        return response()->json([
            'message' => 'تمت إضافة الراتب بنجاح!',
            'payroll' => $payroll
        ], 201);
    }


    // public function showsalary(Request $request)
    // {  
    //     $employeeId = $request->employee_id;
    //     $month = $request->month;
    //     $year = $request->year;
       
    //     $payroll = Payroll::calculateSalary1($employeeId, $month, $year);
    //     return response()->json($payroll);

    // }

    // ========================================

    public function storePayroll(Request $request)
{
    $employeeId = $request->employee_id;
    $month = $request->month;
    $year = $request->year;

    // حساب الراتب
    $payrollData = Payroll::calculateSalary($employeeId, $month, $year);

    return response()->json(['message' => 'تم تسجيل الراتب بنجاح', 'payroll' => $payrollData]);
}

// --------------------حساب المرتب لكل الموظفين----------------------------------
public function processPayroll() 
{
  
    // $month = ($month ?? now()->subMonth()->month)+1;
    $month = ($month ?? now()->subMonth()->month)+1;
    
    $year = $year ?? now()->subMonth()->year;
     
    $employees = User::all();
     
    // foreach ($employees as $employee) {
       
    //     $totalAllowances = Allowances::where('employee_id', $employee->id)
    //         ->whereMonth('effective_date', $month)
    //         ->whereYear('effective_date', $year)
    //         ->sum('amount');

    //     $absences = Absences::where('employee_id', $employee->id)             
    //         ->whereMonth('absence_date', $month)
    //         ->whereYear('absence_date', $year)
    //         ->sum('deduction_amount');
                          
    //         $deductions = Deductions::where('employee_id', $employee->id)
    //         ->whereMonth('effective_date', $month)
    //         ->whereYear('effective_date', $year)
    //         ->sum('amount');

    //     // حساب الراتب الصافي
    //     $totalDeductions = $deductions + $absences;
    //     $netSalary = $employee->salary + $totalAllowances - $totalDeductions;

    //     // إدخال بيانات الراتب في جدول `payrolls`
    //     Payroll::updateOrCreate(
    //         ['employee_id' => $employee->id, 'month' => $month, 'year' => $year],
    //         [
    //             'basic_salary' => $employee->salary,
    //             'total_allowances' => $totalAllowances,
    //             'total_deductions' => $totalDeductions,
    //             'net_salary' => $netSalary,
    //             'payment_status' => 'unpaid',
    //         ]
    //     );
    // }

    // return response()->json(['message' => 'تم احتساب المرتبات بنجاح!']);

    
    $employeeIds = $employees->pluck('id')->toArray();

    $allowances = Allowances::whereIn('employee_id', $employeeIds)
        ->whereMonth('effective_date', $month)
        ->whereYear('effective_date', $year)
        ->get()
        ->groupBy('employee_id');

      

    $absences = Absences::whereIn('employee_id', $employeeIds)
        ->whereMonth('absence_date', $month)
        ->whereYear('absence_date', $year)
        ->get()
        ->groupBy('employee_id');

    $deductions = Deductions::whereIn('employee_id', $employeeIds)
        ->whereMonth('effective_date', $month)
        ->whereYear('effective_date', $year)
        ->get()
        ->groupBy('employee_id');

        
    foreach ($employees as $employee) {
        // $totalAllowances = $allowances[$employee->id]->sum('amount') ?? 0;
        // $totalAbsences = $absences[$employee->id]->sum('deduction_amount') ?? 0;
        // $totalDeductions = $deductions[$employee->id]->sum('amount') ?? 0;
        // $netSalary = $employee->salary + $totalAllowances - ($totalDeductions + $totalAbsences);
        

        $totalAllowances = isset($allowances[$employee->id]) ? $allowances[$employee->id]->sum('amount') : 0;
        $totalAbsences = isset($absences[$employee->id]) ? $absences[$employee->id]->sum('deduction_amount') : 0;
        $totalDeductions = isset($deductions[$employee->id]) ? $deductions[$employee->id]->sum('amount') : 0;

        $netSalary = $employee->salary + $totalAllowances - ($totalDeductions + $totalAbsences);
        Payroll::updateOrCreate(
            ['employee_id' => $employee->id, 'month' => $month, 'year' => $year],
            [
                'basic_salary' => $employee->salary,
                'total_allowances' => $totalAllowances,
                'total_deductions' => $totalDeductions + $totalAbsences, 
                'net_salary' => $netSalary,
                'payment_status' => 'unpaid',
            ]
        );
    }

    return response()->json(['message' => 'تم احتساب المرتبات بنجاح!']);
}

/////////////////////    111

public function getEmployeeDeductions(Request $request, $employeeId)
{  
    $startDate = $request->start_date;
    $endDate = $request->end_date;   

        $deductions = Deductions::where('employee_id', $employeeId)
        ->whereBetween('effective_date', [$startDate, $endDate])
        ->orderBy('effective_date', 'asc')
        ->get();

        $allowances = Allowances::where('employee_id', $employeeId)
        ->whereBetween('effective_date', [$startDate, $endDate])
        ->orderBy('effective_date', 'asc')
        ->get();

        $absences = Absences::where('employee_id', $employeeId)            
        ->whereBetween('absence_date',[$startDate, $endDate])
        ->orderBy('absence_date', 'asc')
        ->get();

                      

        
        $totalAllowances = $allowances->sum('amount');
        $totalDeductions = $deductions->sum('amount');
        $totalAbsences = $absences->sum('deduction_amount');

    return response()->json([
        'deductions' => $deductions,
        'allowances' => $allowances,
        'absences' => $absences,
        'totalAllowances' => $totalAllowances,
        'totalDeductions' => $totalDeductions,
        'totalAbsences' => $totalAbsences,
    
    ]);
  
}

public function getEmployeeAllowances(Request $request, $employeeId)
{  
    $startDate = $request->start_date;
    $endDate = $request->end_date;   

        $allowances = Allowances::where('employee_id', $employeeId)
        ->whereBetween('effective_date', [$startDate, $endDate])
        ->orderBy('effective_date', 'asc')
        ->get();                     

        
        $totalAllowances = $allowances->sum('amount');
    

    return response()->json([         
        'allowances' => $allowances,      
        'totalAllowances' => $totalAllowances,   
    
    ]);
  
}

//-==================================


public function storedatachart(Request $request)
{
    $month = ($month ?? now()->subMonth()->month)+1;
    
    $year = $year ?? now()->subMonth()->year;
     
    $employees = User::all();

    $employeeIds = $employees->pluck('id')->toArray();

    $allowances = Allowances::whereIn('employee_id', $employeeIds)
        ->whereMonth('effective_date', $month)
        ->whereYear('effective_date', $year)
        ->get()
        ->groupBy('employee_id');

      

    $absences = Absences::whereIn('employee_id', $employeeIds)
        ->whereMonth('absence_date', $month)
        ->whereYear('absence_date', $year)
        ->get()
        ->groupBy('employee_id');

    $deductions = Deductions::whereIn('employee_id', $employeeIds)
        ->whereMonth('effective_date', $month)
        ->whereYear('effective_date', $year)
        ->get()
        ->groupBy('employee_id');

       $Alldatachart=[] ;
    foreach ($employees as $employee) {   

        $totalAllowances = isset($allowances[$employee->id]) ? $allowances[$employee->id]->sum('amount') : 0;
        $totalAbsences = isset($absences[$employee->id]) ? $absences[$employee->id]->sum('deduction_amount') : 0;
        $totalDeductions = isset($deductions[$employee->id]) ? $deductions[$employee->id]->sum('amount') : 0;

        $netSalary = $employee->salary + $totalAllowances - ($totalDeductions + $totalAbsences);
                          
            $datachart=[
                'name' => $employee->name,
                'employee_id' => $employee->id, 'month' => $month, 'year' => $year,
                'basic_salary' => $employee->salary,
                'total_allowances' => $totalAllowances,
                'total_deductions' => $totalDeductions , 
                'totalAbsences' => $totalAbsences, 
                'net_salary' => $netSalary,               

            ];            
             
            $Alldatachart[] = $datachart;
    }

    return  response()->json($Alldatachart);
    // return response()->json(['message' => 'تم احتساب المرتبات بنجاح!']);
}
 

public function getpayrollChart(Request $request)
{
    $month = ($month ?? now()->subMonth()->month) + 1;
    $year = $year ?? now()->subMonth()->year;

    $perPage = $request->input('limit', 100);
    $page = $request->input('page', 1);

    $employeeQuery = User::query();
    $totalEmployees = $employeeQuery->count();
    $lastPage = ceil($totalEmployees / $perPage);

    // ✅ تصحيح الصفحة في حال كانت أكبر من الحد الأقصى
    if ($page > $lastPage && $lastPage > 0) {
        $page = $lastPage;
    }

    $employees = $employeeQuery->paginate($perPage, ['*'], 'page', $page);

    $employeeIds = $employees->pluck('id')->toArray();

    $allowances = Allowances::whereIn('employee_id', $employeeIds)
        ->whereMonth('effective_date', $month)
        ->whereYear('effective_date', $year)
        ->get()
        ->groupBy('employee_id');

    $absences = Absences::whereIn('employee_id', $employeeIds)
        ->whereMonth('absence_date', $month)
        ->whereYear('absence_date', $year)
        ->get()
        ->groupBy('employee_id');

    $deductions = Deductions::whereIn('employee_id', $employeeIds)
        ->whereMonth('effective_date', $month)
        ->whereYear('effective_date', $year)
        ->get()
        ->groupBy('employee_id');

    $Alldatachart = [];

    foreach ($employees as $employee) {
        $totalAllowances = isset($allowances[$employee->id]) ? $allowances[$employee->id]->sum('amount') : 0;
        $totalAbsences = isset($absences[$employee->id]) ? $absences[$employee->id]->sum('deduction_amount') : 0;
        $totalDeductions = isset($deductions[$employee->id]) ? $deductions[$employee->id]->sum('amount') : 0;

        $netSalary = $employee->salary + $totalAllowances - ($totalDeductions + $totalAbsences);

        $datachart = [
            'name' => $employee->name,
            'employee_id' => $employee->id,
            'month' => $month,
            'year' => $year,
            'basic_salary' => $employee->salary,
            'total_allowances' => $totalAllowances,
            'total_deductions' => $totalDeductions,
            'totalAbsences' => $totalAbsences,
            'net_salary' => $netSalary,
        ];

        $Alldatachart[] = $datachart;
    }

    return response()->json([
        'data' => $Alldatachart,
        'pagination' => [
            'current_page' => $employees->currentPage(),
            'last_page' => $employees->lastPage(),
            'per_page' => $employees->perPage(),
            'total' => $employees->total(),
        ]
    ]);
}

 
}
