<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiledTask extends Model
{
    use HasFactory;
    protected $fillable = [
        'task_id',
        'id_sender',
        'name_sender',
        'id_receiver',        
        'name_receiver',        
        'title',           
        'file_paths'
    ];

    public function tasks()
    {
        return $this->belongsTo(Task::class);
    }

   // ---------------------------------------------
   public function post()
   {
    return $this->belongsTo(Task::class);
    //    return Storage::url($this->path) ;
   }
}

