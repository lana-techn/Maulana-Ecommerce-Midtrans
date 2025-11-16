@extends('layouts.layouts-landing')

@section('title', 'My Orders')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-900">My Orders</h1>
            <a href="/" class="text-emerald-600 hover:text-emerald-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Continue Shopping
            </a>
        </div>

        @if($orders->count() > 0)
            <div class="space-y-6">
                @foreach($orders as $order)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Order #{{ $order->order_code }}</h3>
                                    <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'paid') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'processing') bg-purple-100 text-purple-800
                                        @elseif($order->status === 'shipped') bg-indigo-100 text-indigo-800
                                        @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    <span class="text-lg font-semibold text-gray-900">
                                        {{ number_format($order->total_amount, 0, ',', '.') }} IDR
                                    </span>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        @foreach($order->items->take(3) as $item)
                                            <div class="flex items-center space-x-2">
                                                @if($item->product && $item->product->image)
                                                    <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                         alt="{{ $item->product->name }}" 
                                                         class="w-10 h-10 object-cover rounded">
                                                @endif
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $item->product->name ?? 'Product Deleted' }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        @if($order->items->count() > 3)
                                            <span class="text-sm text-gray-500">
                                                +{{ $order->items->count() - 3 }} more items
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center space-x-3">
                                        @if($order->status === 'pending')
                                            <button onclick="cancelOrder({{ $order->id }})" 
                                                    class="px-3 py-1 text-sm text-red-600 hover:text-red-700">
                                                Cancel Order
                                            </button>
                                        @endif
                                        
                                        @if($order->status === 'pending' && $order->snap_token)
                                            <button onclick="payOrder('{{ $order->snap_token }}')" 
                                                    class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">
                                                Pay Now
                                            </button>
                                        @endif
                                        
                                        <a href="{{ route('user.orders.show', $order) }}" 
                                           class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No orders yet</h3>
                <p class="text-gray-500 mb-6">You haven't placed any orders yet.</p>
                <a href="/" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    Start Shopping
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ config('midtrans.payment_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
function payOrder(snapToken) {
    window.snap.pay(snapToken, {
        onSuccess: function(result) {
            window.location.reload();
        },
        onPending: function(result) {
            window.location.reload();
        },
        onError: function(result) {
            alert('Payment failed');
        },
        onClose: function() {
            // Payment popup closed
        }
    });
}

function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order?')) {
        fetch(`/user/orders/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert('Error cancelling order');
        });
    }
}
</script>
@endpush
