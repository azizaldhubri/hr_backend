<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\LeaveBalance;

 class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
                'name',                                
                'email',
                'salary',
                'phone_number',
                'national_id',
                'job_title',
                'birth_date',
                'hire_date',
                'nationality', 
                'department_id', 
                'gender', 
                'employment_type',
                'password',
                'status',
                'role',       
                'role_id',          
                'file_paths',
                'google_id',
                'google_token',
                 
            ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //jjjjjjjjj
    public function leaveBalances()
{
    return $this->hasMany(LeaveBalance::class, 'employee_id');
}

public function department()
{
    return $this->belongsTo(Department::class, 'department_id');
}
public function approver()
{
    return $this->belongsTo(User::class, 'approved_by');
}


 
}
