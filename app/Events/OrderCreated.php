<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The order that was created
     */
    public Order $order;

    /**
     * The tickets associated with this order
     */
    public array $tickets;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, array $tickets = [])
    {
        $this->order = $order;
        $this->tickets = $tickets;
    }
}
