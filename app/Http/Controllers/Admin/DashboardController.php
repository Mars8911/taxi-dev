<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;

/**
 * 後台儀表板
 */
class DashboardController extends Controller
{
    /**
     * 儀表板首頁
     */
    public function index()
    {
        $stats = [
            'users_total' => User::count(),
            'users_passenger' => User::where('role', 'passenger')->count(),
            'users_driver' => User::where('role', 'driver')->count(),
            'orders_total' => Order::count(),
            'orders_matching' => Order::where('status', 'matching')->count(),
            'orders_completed' => Order::where('status', 'completed')->count(),
        ];

        $recentOrders = Order::with(['passenger:id,name', 'driver:id,name'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}
