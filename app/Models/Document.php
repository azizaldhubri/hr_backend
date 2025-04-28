<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
            
    protected $fillable = [
        'document_name',
        'supervising_emp',
        'user_name',        
        'document_id',        
        'start_document',
        'end_document',
        'date_alert',
        'document_type' ,
        'file_paths',
        
    ];
}
