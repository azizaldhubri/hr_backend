<?php
    use App\Http\Controllers\AuthController; 
    use App\Http\Controllers\socialAuthController; 
    use App\Http\Controllers\UsersController; 
    use App\Http\Controllers\RolePermissionController ;
    use App\Http\Controllers\PageController;
    use App\Http\Controllers\RoleController; 
    use App\Http\Controllers\DepartmentContoller;  
    use App\Http\Controllers\LeaveRequestController;
    use App\Http\Controllers\LeaveTypeController;
    use App\Http\Controllers\LeaveBalanceController;
    use App\Http\Controllers\NotificationController;
    use App\Http\Controllers\AbsencesController;
    use App\Http\Controllers\AllowancesController;
    use App\Http\Controllers\PayrollController;
    use App\Http\Controllers\DeductionsController;
    use App\Http\Controllers\financial_transactions;
    use App\Http\Controllers\UsersContoller;
    use App\Http\Controllers\TaskController;
    use App\Http\Controllers\ChiledtaskController;
    use App\Http\Controllers\DocumentController;
    use App\Notifications\LeaveRequestNotification;
    use Illuminate\Support\Facades\Route;

    // Route::middleware('auth:sanctum')->group(function () {
    //     Route::apiResource('leave-requests', LeaveRequestController::class);
    // });
 
    // Route::post('users/add', [UsersController::class,'addUser']);
    // Route::get('users', [UsersController::class,'getAllUsers']);
    // Route::get('user/{id}', [UsersController::class,'getUser']);
    // Route::delete('user/{id}', [UsersController::class,'destroy']);
    // Route::post('/user/edit/{id}', [UsersController::class,'editUser']);
                        
    // Route::get('user', [UsersController::class, 'authUser']);

    
    // Route::middleware('auth:sanctum')->get('/notifications', [NotificationController::class, 'getNotifications']);\

    Route::get('notifications', [NotificationController::class, 'getNotifications']);
    Route::post('Notification_isread/{id}', [NotificationController::class, 'update']);


    Route::get('roles', [RoleController::class, 'getRoles']);
    Route::get('rolesName/{id}', [RoleController::class, 'getRoleId']);
    Route::post('addRole', [RoleController::class, 'store']);
    Route::post('roles/update-permissions', [RoleController::class, 'updatePermissions']);
    Route::get('roles/{roleId}', [RoleController::class, 'getPermissionsByRole']);

    Route::get('roles/{user_role}', [RoleController::class, 'getPermissionsByRole_name']);
    Route::get('pages', [PageController::class, 'getPages']);
    Route::post('addPage', [PageController::class, 'store']);
    Route::get('permissions/{roleId}', [RolePermissionController::class, 'getPermissionsByRole']);

    
    // Route::post('/register', [AuthController::class, 'register']);
    // Route::post('/login', [AuthController::class, 'login']);
   
    // Route::post('/register', 'register');
    // Public Routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
        Route::post('/passowrd', 'sendResetLink');
        Route::post('/reset-password', 'reset');
    });

    Route::get('/login-google', [socialAuthController::class, 'redirectToProvider']);
    Route::get('/auth/google/callback', [socialAuthController::class, 'handleCallback']);
    // Route::get('/task', 'GetTasks');
    // Protected Routes
    Route::middleware('auth:api')->group(function () {
        // Users
        Route::get('/user', [UsersController::class, 'authUser']);  
        // Route::post('/leave-requests/{id}/status', [LeaveRequestController::class, 'updateStatus']);   

        Route::middleware('checkAdmin')->controller(UsersController::class)->group(function () {
            Route::get('/users', 'getAllUsers');        
            Route::get('/users_pignate', 'getAllUserspignate');        
            Route::get('/user/{id}', 'getUser');
            Route::post('/user/search', 'search');
            Route::post('/user/edit/{id}', 'editUser');
            Route::post('/users/add', 'addUser');
            Route::delete('/user/{id}', 'destroy');
            

            // Route::get('/employee-stats', 'getEmployeeStats');
        });
        // Product Manger
        
        
        Route::get('departments', [DepartmentContoller::class, 'getAllDepartment']);
        Route::get('department/{id}', [DepartmentContoller::class, 'getDepartment']);
        Route::post('departments/add', [DepartmentContoller::class, 'store']);
        Route::delete('departments/{id}', [DepartmentContoller::class, 'destroy']);
        Route::post('departments/edit/{id}', [DepartmentContoller::class, 'editDepartment']);

        Route::get('/employees/counts', [DepartmentContoller::class, 'getCounts']);
        
        
        // Auth
        Route::get('/logout', [AuthController::class, 'logout']);
    });
    
    
    Route::post('/employee-stats', [PayrollController::class, 'storedatachart']);

    Route::post('/employee_pignate', [PayrollController::class, 'getpayrollChart']);
 

    Route::get('leavesType', [LeaveTypeController::class, 'index']);
    // Route::get('leavesType/{id}', [LeaveTypeController::class, 'update1']);
    Route::post('leavesType/{id}', [LeaveTypeController::class, 'update1']);
    Route::get('leavesType/{id}', [LeaveTypeController::class, 'getLeavesType']);

    Route::post('addLeavesType', [LeaveTypeController::class, 'store']);
    Route::delete('deleteLeavesType/{id}', [LeaveTypeController::class, 'destroy']);


    Route::post('leaves/add', [LeaveRequestController::class, 'store']);
    Route::get('/leave-requests', [LeaveRequestController::class, 'index']); 
    
    Route::post('/leave-requests/{id}/status', [LeaveRequestController::class, 'updateStatus']);
    Route::get('/employees-on-leave', [LeaveRequestController::class, 'getEmployeesOnLeave']);
    
    Route::get('/leave-balances', [LeaveBalanceController::class, 'getAllLeaveBalances']);
    Route::get('/leave_requests_Counts', [LeaveRequestController::class, 'getRequestCounts']); 
    Route::get('/leave_Leave_Counts', [LeaveRequestController::class, 'getLeaveCounts']); 

    // Route::middleware('auth:sanctum')->group(function () {
    //     Route::apiResource('leave-requests', LeaveRequestController::class);
    // });

//-------------------------ادراة المرتبات
    Route::get('/payrolls', [PayrollController::class, 'getPayrolls']);
    Route::post('/deductions', [PayrollController::class, 'addDeduction']);
    Route::post('/absences', [PayrollController::class, 'addAbsence']);
    Route::post('/allowances', [PayrollController::class, 'addallowances']);
    // Route::post('/calculate-salary', [PayrollController::class, 'calculateSalary']);

    Route::get('/employee/{id}/deductions', [PayrollController::class, 'getEmployeeDeductions']);
    Route::get('/employee/{id}/Allowances', [PayrollController::class, 'getEmployeeAllowances']);
    

   
 
    Route::apiResource('financial-transactions', financial_transactions::class);

    // - apiResource-الكود  الاول  يعادل الطرق التالية 
//     Route::get('/financial-transactions', [FinancialTransactionController::class, 'index']);   // عرض جميع السجلات
// Route::post('/financial-transactions', [FinancialTransactionController::class, 'store']);  // إضافة سجل جديد
// Route::get('/financial-transactions/{id}', [FinancialTransactionController::class, 'show']); // عرض سجل معين
// Route::put('/financial-transactions/{id}', [FinancialTransactionController::class, 'update']); // تحديث سجل معين
// Route::delete('/financial-transactions/{id}', [FinancialTransactionController::class, 'destroy']); // حذف سجل معين
    
    //----------------- حساب رواتب الموظفي
    Route::post('/process_payroll', [PayrollController::class, 'processPayroll']);


    Route::post('/payrolls', [PayrollController::class, 'store']);
    
    Route::post('/calculate-salary', [PayrollController::class, 'showsalary']);
    Route::post('store-payroll', [PayrollController::class, 'storePayroll']);


    //--------------------
Route::get('tasks', [TaskController::class, 'index']);
Route::get('tasks/{id}', [TaskController::class, 'show']);
Route::post('tasks/add', [TaskController::class, 'store']);
Route::put('tasks/status_update/{id}', [TaskController::class, 'updateStatus']);
Route::delete('/tasks/{id}',[TaskController::class, 'destroy']);
Route::post('/tasks/edit/{id}',[TaskController::class, 'editpost']);
Route::get('download/{file}', [TaskController::class,'download']);


//--------------------

Route::get('chiled_task', [ChiledtaskController::class,'index']);
Route::post('chiled_task/add', [ChiledtaskController::class,'store']);
Route::get('chiled_task/{id}', [ChiledtaskController::class,'show']);
Route::post('chiled_task/edit/{id}', [ChiledtaskController::class,'update']);
Route::post('chiled_task/edittask/{id}', [ChiledtaskController::class,'edit']);
Route::delete('chiled_task/{id}',[ChiledtaskController::class, 'destroy']);
// Route::post('/product/edit/{id}', 'update');

Route::post('chiled_file/add', [Chiled_files_contoller::class,'store']);
Route::delete('chiled_file/{id}', [Chiled_files_contoller::class,'destroy']);
Route::get('chiled_file', [Chiled_files_contoller::class,'show']);



// Route::get('documents', [DocumentController::class,'index']);
Route::get('serchTypeDocument', [DocumentController::class,'searchDocumnts']);
Route::post('document/add', [DocumentController::class,'store']);
// Route::get('documentupdate/{id}', [DocumentController::class,'getDocument']);
Route::get('documentshow/{id}', [DocumentController::class,'documentshow']);
Route::post('documentupdate/{id}', [DocumentController::class,'updateDocument']);
Route::delete('delete-file', [DocumentController::class, 'deleteFile']);
Route::delete('document/{id}', [DocumentController::class, 'destroy']);
// Route::post('document/edit/{id}', [DocumentController::class,'update']);