<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * 後台 - 訂單管理
 */
class OrderController extends Controller
{
    /**
     * 訂單列表
     */
    public function index(Request $request)
    {
        $query = Order::with(['passenger:id,name,phone', 'driver:id,name,phone']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qry) use ($q) {
                $qry->where('start_location', 'like', "%{$q}%")
                    ->orWhere('end_location', 'like', "%{$q}%")
                    ->orWhereHas('passenger', function ($p) use ($q) {
                        $p->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%");
                    });
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }
}
