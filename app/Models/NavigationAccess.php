<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class NavigationAccess extends Model
{
    protected $fillable = [
        'role_id',
        'description',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}

