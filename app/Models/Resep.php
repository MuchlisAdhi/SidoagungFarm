<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Resep extends BaseModel
{
    use HasFactory;
    protected $table = 'resep';
    protected $guarded = [];
}
