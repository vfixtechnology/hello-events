<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'expires_at',
        'max_uses',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
