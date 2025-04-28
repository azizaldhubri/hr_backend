<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\ChiledTask;  
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;



class ChiledtaskController extends Controller
{
    public function index(Request $request)
    {
        $chiledtask=ChiledTask::all();
        $data=[
            'status'=>200 ,
            'post'=>$chiledtask
    
        ];
        return response()->json($data,200) ;
      
    }
    public function show($id)
    {
        return ChiledTask::where('id', $id)->get();
    }

//     public function show($id)
// {
//     // الحصول على السجل بناءً على الـ ID
//     $record = ChiledTask::findOrFail($id);

//     // تحويل JSON المخزن إلى مصفوفة PHP
//     $filePaths = json_decode($record->file_paths, true);

//     // عرض الملفات (في طريقة العرض أو JSON أو ما يناسبك)
//     return response()->json([
//         'task_id'=>$record->task_id,
//         'id_sender'=>$record->id_sender,
//         'name_sender'=>$record->name_sender,
//         'id_receiver'=>$record->id_receiver,
//         'name_receiver'=>$record->name_receiver,
//         'title'=>$record->title,       
//         'created_at'=>$record->created_at,
//         'updated_at'=>$record->updated_at,        
//         'file_paths'=>$filePaths ]);
// }




    
    public function download(Request $request,$file)
    {
        return response()->download(public_path('backend/public/storage/assets/'.$file));  
          
              
     
    }



    public function store(Request $request)
    {
       
        // معالجة وحفظ الملفات
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
        ChiledTask::create([
            'task_id'  => $request->input('task_id'),
            'id_sender'  => $request->input('id_sender'),
            'name_sender' => $request->input('name_sender'),          
            'id_receiver' => $request->input('id_receiver'),
            'name_receiver' => $request->input('name_receiver'),           
            'title' => $request->input('title'),            
            'file_paths' => json_encode($filePaths), // حفظ المسارات كـ JSON في حقل واحد
        ]);

        return response()->json(['message' => 'Files uploaded successfully'], 200);
    }
}
