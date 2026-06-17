<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:order list')->only(['index']);
        $this->middleware('can:order show')->only(['show']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $perPage = in_array((int) $request->input('per_page'), [20, 50, 100]) ? (int) $request->input('per_page') : 20;

        $orders = Order::with('user', 'tickets')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhere('buyer_name', 'like', "%{$search}%")
                      ->orWhere('buyer_email', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('backend.order.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'tickets.ticketType.event', 'tickets.addOns');
        return view('backend.order.show', compact('order'));
    }

}
