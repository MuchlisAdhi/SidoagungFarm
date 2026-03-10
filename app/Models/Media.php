<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends BaseModel
{
    use HasFactory;

    protected $table = 'medias';

    protected $guarded = [];   
}
