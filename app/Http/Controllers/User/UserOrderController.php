<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class UserOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items.product'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Pastikan user hanya bisa melihat order miliknya sendiri
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['items.product', 'user']);

        return view('user.orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        // Pastikan user hanya bisa cancel order miliknya sendiri
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to this order.'
            ], 403);
        }

        // Hanya order dengan status pending yang bisa dibatalkan
        if ($order->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only pending orders can be cancelled.'
            ], 400);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'status' => 'success',
            'message' => 'Order has been cancelled successfully.'
        ]);
    }
}
