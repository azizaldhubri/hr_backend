<?php
namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Task;  
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ChiledTask;

class TaskController extends Controller
{  
    // public function index(Request $request)
    // {
    //     $task=Task::with('chiledtask')->get();
    //     $data=[
    //         'status'=>200 ,
    //         'post'=>$task    
    //     ];
    //     return response()->json($data,200) ;     
    // }

//     public function index(Request $request)
// {
//     $items = Task::with('chiledtask')->paginate(3);
//     $data=[
//                 'status'=>200 ,
//                 'post'=>$items    
//             ];
//     return response()->json($data);
// }

   
  


public function updateStatus(Request $request, $id)
{ 
    $task = Task::find($id);
    if ($task) {

        $task->task_status = $request->status; // مثلاً 'completed'
        $task->save();
        return response()->json(['message' => 'Task status updated to completed']);
    }

    return response()->json(['message' => 'Task not found'], 404);
}

public function show($id)
{
    // $record = Task::where('id', $id)->with('chiledtask')->get();
    $postId=$id;
    $record = Task::with('chiledtask')->find($postId);

    if ($record) {
        // التحقق من وجود المرفقات وعدم كونها فارغة
        $postAttachments = !empty($record->file_paths) ? json_decode($record->file_paths, true) : [];
    
        // بناء مصفوفة للـ post
        $postArray = [
            'description'=>$record->description,
            'sender_id'=>$record->sender_id,
            'sender_name'=>$record->sender_name,
            'id_receiver'=>$record->id_receiver,
            'receiver_name'=>$record->receiver_name,
            'start_task'=>$record->start_task,
            'end_task'=>$record->end_task,
            'task_status'=>$record->task_status,
            'task_type'=>$record->task_type,
            'created_at'=>$record->created_at,
            'updated_at'=>$record->updated_at,        
            'file_paths'=>$postAttachments ,            
            'comments' => []
        ];
    
        foreach ($record->chiledtask  as $comment) {
            // التحقق من وجود المرفقات وعدم كونها فارغة
            $commentAttachments = !empty($comment->file_paths) ? json_decode($comment->file_paths, true) : [];
    
            // بناء مصفوفة لكل comment
            $commentArray = [
                'task_id'=>$comment->task_id,
                'id_sender'=>$comment->id_sender,
                'name_sender'=>$comment->name_sender,
                'id_receiver'=>$comment->id_receiver,
                'name_receiver'=>$comment->name_receiver,
                'created_at'=>$comment->created_at,
                'updated_at'=>$comment->updated_at,
                'title'=>$comment->title,        
                'file_paths'=>$commentAttachments
            ];
    
            // إضافة comment إلى مصفوفة الـ post
            $postArray['comments'][] = $commentArray;
        }
    
        // عرض المصفوفة الناتجة
        // print_r($postArray);
        return  response()->json($postArray);

    } else {
        return "Post not found.";
    }


}



    
    public function download(Request $request,$file)
    {
        return response()->download(public_path('storage/assets/'.$file));      
               
     
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
        Task::create([
            'sender_id'  => $request->input('sender_id'),
            'sender_name' => $request->input('sender_name'),          
            'id_receiver' => $request->input('id_receiver'),
            'receiver_name' => $request->input('receiver_name'),
            'task_status' => $request->input('task_status'),
            'task_type' => $request->input('task_type'),
            'description' => $request->input('description'),
            'start_task' => $request->input('start_task'),
            'end_task' => $request->input('end_task'),
            'file_paths' => json_encode($filePaths), // حفظ المسارات كـ JSON في حقل واحد
        ]);

        return response()->json(['message' => 'Files uploaded successfully'], 200);
    }
    
    public function destroy($id)
    {           
        return  Task::findOrFail($id)->delete();
    }

    public function editpost(Request $request, $id)
    {
        $request->validate([
            'description' => 'required'
            
        ]);
        $task = Task::findOrFail($id);
        $task->description = $request->description;
     
        $task->save();
    }

    //----------------------------------------------------------------------------------------------
    public function index(Request $request)
{
    // $record = Task::where('id', $id)->with('chiledtask')->get();
   
    $record1 = Task::with('chiledtask')->get() ;
    // return  $record ;
    $postArray1=[];
    if ($record1) {
        // التحقق من وجود المرفقات وعدم كونها فارغة
        foreach ($record1 as $record ) {             
        $postAttachments = !empty($record->file_paths) ? json_decode($record->file_paths, true) : [];    
        // بناء مصفوفة للـ post
        $postArray = [            
            'id'=>$record->id,
            'description'=>$record->description,
            'sender_id'=>$record->sender_id,
            'sender_name'=>$record->sender_name,
            'id_receiver'=>$record->id_receiver,
            'receiver_name'=>$record->receiver_name,
            'start_task'=>$record->start_task,
            'end_task'=>$record->end_task,
            'task_status'=>$record->task_status,
            'task_type'=>$record->task_type,
            'created_at'=>$record->created_at,
            'updated_at'=>$record->updated_at,        
            'file_paths'=>$postAttachments ,            
            'chiledtask' => []
        ];
    
        foreach ($record->chiledtask  as $comment) {
            // التحقق من وجود المرفقات وعدم كونها فارغة
            $commentAttachments = !empty($comment->file_paths) ? json_decode($comment->file_paths, true) : [];
    
            // بناء مصفوفة لكل comment
            $commentArray = [
                'task_id'=>$comment->task_id,
                'id_sender'=>$comment->id_sender,
                'name_sender'=>$comment->name_sender,
                'id_receiver'=>$comment->id_receiver,
                'name_receiver'=>$comment->name_receiver,
                'created_at'=>$comment->created_at,
                'updated_at'=>$comment->updated_at,
                'title'=>$comment->title,        
                'file_paths'=>$commentAttachments
            ];
    
            // إضافة comment إلى مصفوفة الـ post
            $postArray['chiledtask'][] = $commentArray;
        }
    
        // عرض المصفوفة الناتجة
        // print_r($postArray);
        $postArray1['postArray'][] = $postArray;
    }
    return  response()->json($postArray1);
    
    } else {
        return "Post not found.";
    }



}

// public function index()
// {
//     // جلب جميع المنشورات مع الصور
//     $posts = Task::with('images')->get();

//     return response()->json([
//         'status' => 'success',
//         'data' => $posts,
//     ], 200);

//     // $posts = Task::with(['images' => function ($query) {
//     //     $query->select('id',  'path', 'url');  
//     //     // $query->select('*');  
//     // }])->get();
// }
}
