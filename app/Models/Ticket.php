<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ticket extends Model
{
    protected $guarded = [];

    protected $casts = [
        'other' => 'array',
        'checked_in_at' => 'datetime',
        'first_check_in_at' => 'datetime',
        'last_check_in_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    public function addOns(): BelongsToMany
    {
        return $this->belongsToMany(AddOn::class);
    }
}
