<?php

namespace App\Http\Controllers;
use App\Models\Pages;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function getPages()
    {
        $pages = Pages::all(); // جلب جميع الصفحات
        return response()->json($pages);
    }
    
    public function store(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validatedData = $request->validate([
            'name' => 'required|string|unique:roles|max:255', // تحقق من فريدة الاسم
             
        ]);

        // إنشاء دور جديد باستخدام البيانات
        $page = Pages::create([
            'name' => $validatedData['name'],             
        ]);

        // إرسال استجابة عند النجاح
        return response()->json([
            'message' => 'تمت إضافة الصفحة بنجاح',
            'page' => $page
        ], 201);
    }

    //-------------------------- محاولة اختبار الصلاحية
}
