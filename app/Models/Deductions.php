<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deductions extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'deduction_type',
        'amount',
        'effective_date',         
    ];
    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
