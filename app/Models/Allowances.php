<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allowances extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'effective_date',
        'allowance_type',
        'amount',
         
    ];
    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
