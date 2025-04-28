<?php

namespace App\Http\Controllers;
use Carbon\Carbon; 
use App\Models\Document;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $document=Document::all();
        $data=[
            'status'=>200 ,
            'post'=>$document
    
        ];
        return response()->json($data,200) ;
      
    }
//-------------------------------------------------------------------------------------------------
    public function GetDocumnts(Request $request)
    {
        $Documents = Document::paginate($request->input('limit', 100));
        return $Documents;
    }
    //------------------------------------



    public function searchDocumnts(Request $request)
    {
        // الحصول على كلمة البحث من الطلب
        $searchTerm = $request->query('query');
        $limit = $request->query('limit');
         
        // البحث في قاعدة البيانات مع استخدام التصفيح
        $results = Document::where('document_type', 'LIKE', '%' . $searchTerm . '%')
                             ->paginate($limit ); // عدد العناصر لكل صفحة
                             return response()->json($results);
 
    }

    //------------------------------------------------------------------------------------------------------

    public function show($id)
    {
        return Document::where('id', $id)->get();
    }

    //-------------------------------------000000000000000000000000000000000000000000000000000000000000
    // public function document_show($id)
    // {
    //     // $record = Task::where('id', $id)->with('chiledtask')->get();
    //     $postId=$id;
    //     $record = Document::find($postId);
    
    //     if ($record) {
    //         // التحقق من وجود المرفقات وعدم كونها فارغة
    //         $postAttachments = !empty($record->file_paths) ? json_decode($record->file_paths, true) : [];
        
        
        
    //         $postArray = [
    //             'document_name'=>$record->document_name,
    //             'supervising_emp'=>$record->supervising_emp,
    //             'user_name'=>$record->user_name,
    //             'document_id'=>$record->document_id,
    //             'start_document'=>$record->start_document,
    //             'end_document'=>$record->end_document,
    //             'date_alert'=>$record->date_alert,
    //             'document_type'=>$record->document_type,            
    //             'created_at'=>$record->created_at,
    //             'updated_at'=>$record->updated_at,        
    //             'file_paths'=>$postAttachments ,            
    //             'comments' => []
    //         ];
        
    //         foreach ($record->chiledtask  as $comment) {
    //             // التحقق من وجود المرفقات وعدم كونها فارغة
    //             $commentAttachments = !empty($comment->file_paths) ? json_decode($comment->file_paths, true) : [];
        
    //             // بناء مصفوفة لكل comment
    //             $commentArray = [
    //                 'task_id'=>$comment->task_id,
    //                 'id_sender'=>$comment->id_sender,
    //                 'name_sender'=>$comment->name_sender,
    //                 'id_receiver'=>$comment->id_receiver,
    //                 'name_receiver'=>$comment->name_receiver,
    //                 'created_at'=>$comment->created_at,
    //                 'updated_at'=>$comment->updated_at,
    //                 'title'=>$comment->title,        
    //                 'file_paths'=>$commentAttachments
    //             ];
        
    //             // إضافة comment إلى مصفوفة الـ post
    //             $postArray['comments'][] = $commentArray;
    //         }
        
    //         // عرض المصفوفة الناتجة
    //         // print_r($postArray);
    //         return  response()->json($postArray);
    
    //     } else {
    //         return "Post not found.";
    //     }
    
    
    // }


    //-------------

    public function destroy($id)
    {
        return  Document::findOrFail($id)->delete();
    }
         public function documentshow($id)
{
    // الحصول على السجل بناءً على الـ ID
    $record = Document::findOrFail($id);

    // تحويل JSON المخزن إلى مصفوفة PHP
    $filePaths = json_decode($record->file_paths, true);

    // عرض الملفات (في طريقة العرض أو JSON أو ما يناسبك)
    return response()->json([
       'document_name'=>$record->document_name,
                'supervising_emp'=>$record->supervising_emp,
                'user_name'=>$record->user_name,
                'document_id'=>$record->document_id,
                'start_document'=>$record->start_document,
                'end_document'=>$record->end_document,
                'date_alert'=>$record->date_alert,
                'document_type'=>$record->document_type,            
                'created_at'=>$record->created_at,
                'updated_at'=>$record->updated_at,        
                'file_paths'=>$filePaths ]);
}
    
    //-------------------------------------000000000000000000000000000000000000000000000000000000000000
    public function getDocument($id)
    {
        return Document::findOrFail($id);
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
        Document::create([
            'document_name'  => $request->input('document_name'),
            'supervising_emp'  => $request->input('supervising_emp'),
            'user_name' => $request->input('user_name'),          
            'document_id' => $request->input('document_id'),
            'start_document' => $request->input('start_document'),           
            'end_document' => $request->input('end_document'),            
            'date_alert' => $request->input('date_alert'),            
            'document_type' => $request->input('document_type'),            
            'file_paths' => json_encode($filePaths), // حفظ المسارات كـ JSON في حقل واحد
        ]);

        return response()->json(['message' => 'Files uploaded successfully'], 200);
    }


    public function updateDocument(Request $request,$id)    {   
         $request->validate([        
        // 'file_paths' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        // 'files.*' => 'nullable|file|max:2048',
        'document_name'  => 'required',
        'supervising_emp'  => 'required',
        'user_name' =>  'required',        
        'document_id' => 'required',
        // 'start_document' =>   'required',       
        // 'end_document' =>'required',              
        // 'date_alert' => 'required',            
        'document_type' => 'required',
         ]);

         $request->validate([
            'files.*' => 'nullable|file|mimes:jpg,png,pdf,docx|max:2048',  // التحقق من الملفات
        ]);
        $record = Document::findOrFail($id);
        // معالجة وحفظ الملفات
        $newFilePaths = [];  // مصفوفة لحفظ مسارات الملفات الجديدة

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $timestamp = Carbon::now()->format('YmdHis');  // الحصول على الطابع الزمني
                $originalName = $file->getClientOriginalName();  // الاسم الأصلي للملف
                $newFileName = $timestamp . '_' . $originalName;  // اسم جديد يتضمن الطابع الزمني
        
                // تخزين الملف في مجلد 'uploads'
                // $filepath = $file->storeAs('assets', $newFileName, 'public');
                //             $filePaths[] = $filepath;
                $filePath = $file->storeAs('assets', $newFileName, 'public');
                $newFilePaths[] = $filePath;  // إضافة مسار الملف إلى المصفوفة
            }
        }

         // استخراج المسارات القديمة من قاعدة البيانات (إذا كانت موجودة)
       $existingFilePaths = json_decode($record->file_paths, true) ?? [];
      // دمج المسارات الجديدة مع القديمة
       $allFilePaths = array_merge($existingFilePaths, $newFilePaths); 
            $record-> document_name  = $request->input('document_name');
            $record-> supervising_emp  = $request->input('supervising_emp');
            $record-> user_name = $request->input('user_name');         
            $record-> document_id = $request->input('document_id');
            $record-> start_document = $request->input('start_document');           
            $record-> end_document = $request->input('end_document');           
            $record-> date_alert = $request->input('date_alert');           
            $record-> document_type = $request->input('document_type');             
            $record->file_paths = json_encode($allFilePaths);           
           $record->save();

        return response()->json(['message' => 'Files uploaded successfully'], 200);
    }

 

 

    public function deleteFile(Request $request)
    {      $id = $request->query('id');
        // تحقق من وجود اسم الملف في الطلب
        $fileName = $request->input('file_name');

        // استخرج السجل من قاعدة البيانات
        $record = Document::findOrFail($id);  // تأكد من أن لديك $id المحدد
        $filePaths = json_decode($record->file_paths, true);  // فك ترميز JSON إلى مصفوفة

        // تحقق مما إذا كان اسم الملف موجودًا في المصفوفة
        if (in_array($fileName, $filePaths)) {
            // إزالة اسم الملف من المصفوفة
            $filePaths = array_filter($filePaths, function ($file) use ($fileName) {
                return $file !== $fileName;
            });

            // تحديث المصفوفة في قاعدة البيانات
            $record->file_paths = json_encode(array_values($filePaths));
            $record->save();

            // حذف الملف فعليًا من نظام الملفات
            if (Storage::exists($fileName)) {
                Storage::delete($fileName);
            }

            return response()->json(['message' => 'File deleted successfully.']);
        }

        return response()->json(['message' => 'File not found.'], 404);
    }



}
