<?php

namespace App\Http\Controllers;
use App\Models\RolePermission;
use App\Models\Pages;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function getPermissionsByRole($roleId)
    {
        $permissions = RolePermission::where('role_id', $roleId)
            ->with('page') // جلب الصفحة المرتبطة بكل صلاحية
            ->get();
        return response()->json($permissions);
    }

    protected $fillable = ['role_id', 'page_id', 'can_view', 'can_edit'];

    // علاقة الصلاحية مع الصفحة
    public function page()
    {
        return $this->belongsTo(Pages::class, 'page_id');
    }
}
