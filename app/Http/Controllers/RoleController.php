<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller  
{
    public function getRoles()
    {
        $roles = Roles::all();
        return response()->json($roles);
    }

  public function getRoleId(Request $request,$id)
    {    
        return $role =Roles::findOrFail($id);
           
    }


    public function store(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validatedData = $request->validate([
            'name' => 'required|string|unique:roles|max:255', // تحقق من فريدة الاسم
             
        ]);

        // إنشاء دور جديد باستخدام البيانات
        $role = Roles::create([
            'name' => $validatedData['name'],             
        ]);

        // إرسال استجابة عند النجاح
        return response()->json([
            'message' => 'تمت إضافة الصفحة بنجاح',
            'role' => $role
        ], 201);
    }

    public function getPermissionsByRole($roleId)
    {
        // جلب الصلاحيات المرتبطة بالدور المحدد مع جلب الصفحة المرتبطة بكل صلاحية
        $permissions = RolePermission::where('role_id', $roleId)
            ->with('page') // هنا نفترض أن هناك علاقة بين RolePermission و Page           
            ->get();

        return response()->json($permissions);
    }


    public function getPermissionsByRole_name($roleName)
    {
        // جلب الصلاحيات المرتبطة بالدور المحدد مع جلب الصفحة المرتبطة بكل صلاحية
        $permissions = RolePermission::where('name', $roleName)
            ->with('page') // هنا نفترض أن هناك علاقة بين RolePermission و Page           
            ->get();

        return response()->json($permissions);
    }



    public function updatePermissions(Request $request)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array'
        ]);

        $roleId = $request->input('role_id');
        $permissions = $request->input('permissions'); // شكل البيانات: [page_id => ['can_view' => true, 'can_edit' => false], ...]

        DB::beginTransaction();
        try {
            // حذف الصلاحيات الحالية للدور
            RolePermission::where('role_id', $roleId)->delete();

            // تحديث الصلاحيات حسب البيانات المرسلة
            foreach ($permissions as $pageId => $permissionData) {
                RolePermission::create([
                    'role_id' => $roleId,
                    'page_id' => $pageId,
                    'can_view' => $permissionData['can_view'] ?? false,
                    'can_edit' => $permissionData['can_edit'] ?? false,
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Permissions updated successfully'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update permissions', 'error' => $e->getMessage()], 500);
        }
    }

}
