<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'sender_id',
        'sender_name',
        'id_receiver',        
        'receiver_name',        
        'task_status',
        'task_type',
        'description',
        'start_task' ,
        'end_task',
        'file_paths'
    ];

    
  public function chiledtask()
  {
      return $this->hasMany(ChiledTask::class);
    //   return $this->hasMany(ChiledTask::class,'task_id');
  } 

//----------------------------------------------------------------------------
  public function images()
  {
      return $this->hasMany(ChiledTask::class);
  }

}
