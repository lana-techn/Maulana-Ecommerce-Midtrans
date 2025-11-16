<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderHistoryController extends Controller
{
    public function history(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $orders = Order::query()
            ->when($search, function ($query) use ($search) {
                $query->where('order_code', 'like', "%$search%");
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->with(['user', 'orderItems.product'])
            ->latest()
            ->paginate(10);
        $orders->appends($request->all());

        return view('admin.orders.history', compact('orders'));
    }

    /**
     * Update the status of an order.
     *
     * @param int $id
     * @param string $status
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus($id, $status)
    {
        // Validate status
        $validStatuses = ['processing', 'shipped', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return redirect()->back()->with('error', 'Invalid status provided.');
        }
        $order = Order::findOrFail($id);
        $order->status = $status;
        $order->save();

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }
}
