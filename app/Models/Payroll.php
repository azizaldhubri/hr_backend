<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Payroll extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'basic_salary',
        'total_allowances',
        'total_deductions',         
        'net_salary',
        'payment_status',
    ];   


    public function employee()
    {
        return $this->belongsTo(User::class);
    }

    public static function calculateSalary($employeeId, $month, $year)
    {
        $employee = User::findOrFail($employeeId);
        // $employee = User::findOrFail(2);
        
        // return  'okee';
        $allowances = Allowances::where('employee_id', $employeeId)
        ->whereMonth('effective_date', $month)
        ->whereYear('effective_date', $year)
        ->sum('amount');
        $deductions = Deductions::where('employee_id', $employeeId)
        ->whereMonth('effective_date', $month)
        ->whereYear('effective_date', $year)
        ->sum('amount');
        $absences = Absences::where('employee_id', $employeeId)
                          ->whereMonth('absence_date', $month)
                          ->whereYear('absence_date', $year)
                          ->sum('deduction_amount');

        $totalDeductions = $deductions + $absences;
        $netSalary = $employee->salary + $allowances - $totalDeductions;

        // return self::create([ 
        return self::updateOrCreate([
            'employee_id' => $employeeId,
            'month' => $month,
            'year' => $year,
            'basic_salary' => $employee->salary,
            'total_allowances' => $allowances,           
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'payment_status' => 'unpaid'
        ]);
    }



    // public static function calculateSalary1($employeeId, $month, $year)
    // {
    //     // return $employeeId ;
    //     $employee = User::findOrFail($employeeId);
        
    //     // الراتب الأساسي
    //     $basicSalary = $employee->salary;

    //     // حساب البدلات
    //     $totalAllowances = Allowances::where('employee_id', $employeeId)
    //         ->whereMonth('effective_date', $month)
    //         ->whereYear('effective_date', $year)
    //         ->sum('amount');

    //     // حساب الخصومات
    //     $totalDeductions = Deductions::where('employee_id', $employeeId)
    //         ->whereMonth('effective_date', $month)
    //         ->whereYear('effective_date', $year)
    //         ->sum('amount');

    //     // حساب الخصم بسبب الغياب
    //     $absenceDeductions = Absences::where('employee_id', $employeeId)
    //         ->whereMonth('absence_date', $month)
    //         ->whereYear('absence_date', $year)
    //         ->sum('deduction_amount');

    //     // حساب الراتب الصافي
    //     $netSalary = $basicSalary + $totalAllowances - ($totalDeductions + $absenceDeductions);

    //     return [
    //         'employee_id' => $employeeId,
    //         'month' => $month,
    //         'year' => $year,
    //         'basic_salary' => $basicSalary,
    //         'total_allowances' => $totalAllowances,
    //         'total_deductions' => $totalDeductions + $absenceDeductions,
    //         'net_salary' => $netSalary,
    //         'payment_status' => 'unpaid'
    //     ];
    // }

    
 
}
