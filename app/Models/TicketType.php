<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::deleting(function (TicketType $ticketType) {
            if ($ticketType->tickets()->whereHas('order', fn($q) => $q->whereIn('status', ['pending', 'completed']))->exists()) {
                return false;
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function getAvailableTicketsAttribute(): int
    {
        $soldTickets = $this->tickets()
            ->whereHas('order', function ($query) {
                $query->whereIn('status', ['pending', 'completed']);
            })
            ->count();

        return max(0, $this->quantity - $soldTickets);
    }

    public function getSoldTicketsAttribute(): int
    {
        return $this->tickets()
            ->whereHas('order', function ($query) {
                $query->whereIn('status', ['pending', 'completed']);
            })
            ->count();
    }
}
