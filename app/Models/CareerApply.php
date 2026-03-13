<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerApply extends BaseModel
{
    use HasFactory;
    protected $table = 'careerapply';

    protected $guarded = [];

    /**
     * Get the career that owns the application.
     */
    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class, 'careerid', 'id');
    }
}
