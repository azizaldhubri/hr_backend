<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absences extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'absence_date',
        'absence_type',
        'deduction_amount',        
    ];
    public function users()
    {
        return $this->belongsTo(User::class);
    }
 

    public function employee()
    {
        return $this->belongsTo(User::class);
    }
}
