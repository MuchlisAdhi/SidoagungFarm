<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class News extends BaseModel
{
    use HasFactory;
    protected $table = 'news';
    protected $guarded = []; 
}
