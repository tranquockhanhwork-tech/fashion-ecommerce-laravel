<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'position',
        'salary',
        'hired_at',
        'avatar',
    ];

    protected $casts = [
        'salary' => 'float',
        'hired_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
