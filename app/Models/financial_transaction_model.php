<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class financial_transaction_model extends Model
{
    use HasFactory;
    protected $fillable = ['employee_id', 'type', 'amount', 'description', 'transaction_date'];

    public function employee()
    {
        return $this->belongsTo(User::class);
    }
}
