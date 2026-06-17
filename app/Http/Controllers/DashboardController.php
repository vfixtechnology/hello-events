<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('staff')) {
            return view('backend.dashboard.index', ['showAdminStats' => false, 'showStaffScanner' => true]);
        }

        if ($user->can('dashboard view')) {
            [$dateFrom, $dateTo] = $this->resolveDateRange($request);
            $data = $this->getDashboardData($dateFrom, $dateTo);
            $data['showAdminStats'] = true;

            if ($request->wantsJson() || $request->ajax()) {
                $data['html'] = [
                    'recentOrders'     => view('backend.dashboard.partials.orders', $data)->render(),
                    'orderStatusCounts' => view('backend.dashboard.partials.status-counts', $data)->render(),
                ];
                return response()->json($data);
            }

            return view('backend.dashboard.index', $data);
        }

        $myOrders = Order::where('user_id', $user->id)->latest()->paginate(10, ['*'], 'orders_page');
        $myTickets = Ticket::whereHas('order', fn($q) => $q->where('user_id', $user->id))
            ->with('ticketType.event', 'order')
            ->latest()
            ->paginate(10, ['*'], 'tickets_page');

        return view('backend.dashboard.index', compact('myOrders', 'myTickets') + ['showAdminStats' => false]);
    }

    private function resolveDateRange(Request $request): array
    {
        $period = $request->input('period', 'all');

        return match ($period) {
            'today' => [Carbon::today(), Carbon::today()->endOfDay()],
            'week'  => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'custom' => [
                $request->input('from') ? Carbon::parse($request->input('from'))->startOfDay() : null,
                $request->input('to') ? Carbon::parse($request->input('to'))->endOfDay() : null,
            ],
            default => [null, null],
        };
    }

    private function getDashboardData($dateFrom, $dateTo): array
    {
        $eventQuery = Event::query();
        $orderQuery = Order::query();
        $ticketQuery = Ticket::query();

        if ($dateFrom && $dateTo) {
            $eventQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
            $orderQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
            $ticketQuery->whereHas('order', fn($q) => $q->whereBetween('created_at', [$dateFrom, $dateTo]));
        }

        $totalEvents = (clone $eventQuery)->count();
        $totalOrders = (clone $orderQuery)->count();
        $totalRevenue = (clone $orderQuery)->where('status', 'completed')->sum('grand_total');
        $totalCategories = Category::count();

        $totalTicketsSold = (clone $ticketQuery)
            ->whereHas('order', fn($q) => $q->whereIn('status', ['pending', 'completed']))
            ->count();

        $upcomingEventsCount = (clone $eventQuery)
            ->where('start_datetime', '>', Carbon::now())
            ->count();

        $revenueByStatus = [
            'completed' => (clone $orderQuery)->where('status', 'completed')->sum('grand_total'),
            'pending'   => (clone $orderQuery)->where('status', 'pending')->sum('grand_total'),
            'failed'    => (clone $orderQuery)->where('status', 'failed')->sum('grand_total'),
        ];

        $yearStart = Carbon::now()->startOfYear();
        $yearEnd = Carbon::now()->endOfYear();
        $monthlyRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [$yearStart, $yearEnd])
            ->select(DB::raw('SUM(grand_total) as total'), DB::raw('MONTH(created_at) as month'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = $monthlyRevenue[$i] ?? 0;
        }

        $recentOrders = (clone $ticketQuery)
            ->with(['order', 'ticketType.event'])
            ->latest()
            ->take(10)
            ->get();

        $orderStatusCounts = (clone $orderQuery)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return compact(
            'totalEvents', 'totalCategories', 'totalOrders', 'totalRevenue',
            'totalTicketsSold', 'upcomingEventsCount', 'revenueByStatus',
            'months', 'recentOrders', 'orderStatusCounts'
        );
    }
}
