<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $fillable = [
        'department_name',
        'responsible_manager',
        'description',
        'location',
        'creation_date',
        'Status',
                
    ];
    public function employees()
    {
        return $this->hasMany(User::class, 'department_id');
    }
}
