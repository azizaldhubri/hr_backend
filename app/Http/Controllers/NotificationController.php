<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User ; 
use App\Models\Notification ; 

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function getNotifications()
{
    $notifications = Notification::where('employee_id', auth('api')->id())->orderBy('created_at', 'desc')->get();

    return response()->json($notifications);
   
}
    public function index()
    {
        //
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
        //
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $notification=Notification::findOrfail($id);
        $notification->update([
            'is_read'  => '1' ,]);
            $notification->save();
              return response()->json(['message' => 'تم تحديث حالة الطلب بنجاح!']);
              
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
