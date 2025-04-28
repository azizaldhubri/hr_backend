<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;
    protected $fillable = ['role_id', 'page_id', 'can_view', 'can_edit'];

    public function role()
    {
        return $this->belongsTo(Roles::class);
    }

    public function page()
    {
        return $this->belongsTo(Pages::class);
    }
}
