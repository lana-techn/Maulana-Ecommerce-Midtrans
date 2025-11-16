@extends('layouts.layout-admin')

@section('title', 'Order History')

@section('header_title', 'Order History')
@section('header_subtitle', 'View all orders history')

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Order History</h1>
        </div>

        <!-- Order Table -->
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    {{-- search --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <form action="{{ route('admin.orders.history') }}" method="GET"
                            class="d-flex align-items-center gap-2">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Search orders ID..." value="{{ request('search') }}">
                                <select name="status" class="form-select form-select-sm" id="">
                                    <option value="">All Status</option>
                                    @foreach (['processing', 'shipped', 'completed', 'cancelled'] as $status)
                                        <option value="{{ $status }}"
                                            {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            @if (request('search') || request('status'))
                                <a href="{{ route('admin.orders.history') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Clear
                                </a>
                            @endif
                        </form>
                    </div>

                    {{-- order table --}}
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order Code</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Payment Method</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->order_code }}</td>
                                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->midtrans_payment_type }}</td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                            data-bs-target="#orderDetailModal{{ $order->id }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            @if ($orders->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-inbox display-4 text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No Order Found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    {{-- pagination --}}
                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }}
                        </div>
                        <div>
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- modal --}}
        @foreach ($orders as $order)
            {{-- order detail modal --}}
            <div class="modal fade" id="orderDetailModal{{ $order->id }}" tabindex="-1"
                aria-labelledby="orderDetailModalLabel{{ $order->id }}">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="orderDetailModallabel{{ $order->id }}">
                                <i class="bi bi-box-seam me-2"></i>Order #{{ $order->id }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row gap-4">
                                {{-- order status card --}}
                                <div class="col-12">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <p class="text-muted mb-1">Order Status</p>
                                                    <h4 class="mb-0">
                                                        <span class="badge bg-{{ $order->status_color }}">
                                                            {{ ucfirst($order->status) }}
                                                        </span>
                                                    </h4>
                                                </div>
                                                <div class="text-end">
                                                    <p class="text-muted mb-1">Order Date</p>
                                                    <h6 class="mb-0">{{ $order->created_at->format('d M Y') }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- customer info --}}
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <h6 class="card-title mb-3">
                                                <i class="bi bi-person me-2"></i>Customer Information
                                            </h6>
                                            <div class="mb-2">
                                                <label class="text-muted small">Name</label>
                                                <p class="mb-0">{{ $order->user->name ?? 'N/A' }}</p>
                                            </div>
                                            <div class="mb-2">
                                                <label class="text-muted small">Email</label>
                                                <p class="mb-0">{{ $order->user->email ?? 'N/A' }}</p>
                                            </div>
                                            <div class="mb-0">
                                                <label class="text-muted small">Phone</label>
                                                <p class="mb-0">{{ $order->user->phone ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- shipping info --}}
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <h6 class="card-title mb-3">
                                                <i class="bi bi-truck me-2"></i>Shipping Information
                                            </h6>
                                            <div class="mb-2">
                                                <label class="text-muted small">Address</label>
                                                <p class="mb-1 fw-medium">{{ $order->shipping_address ?? 'N/A' }}</p>
                                            </div>
                                            @if ($order->resi_code)
                                                <div class="mb-0">
                                                    <label class="text-muted small">Tracking Number</label>
                                                    <p class="mb-1 fw-medium">{{ $order->resi_code }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- payment info --}}
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h6 class="card-title mb-3">
                                                <i class="bi bi-credit-card me-2"></i>Payment Information
                                            </h6>
                                            <div class="row gap-3">
                                                @if ($order->midtrans_transaction_id)
                                                    <div class="col-md-4">
                                                        <label class="text-muted small">Transaction ID</label>
                                                        <p class="mb-1 fw-medium">
                                                            {{ $order->midtrans_transaction_id }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="text-muted small">Payment Type</label>
                                                        <p class="mb-1 fw-medium">
                                                            {{ ucwords(str_replace('_', ' ', $order->midtrans_payment_type)) }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- order item --}}
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h6 class="card-title mb-3">
                                                <i class="bi bi-box me-2"></i>Order Items
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table table-borderless align-middle">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Unit Price</th>
                                                            <th>Quantity</th>
                                                            <th>Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($order->orderItems as $item)
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex gap-3">
                                                                        <img src="{{ $item->product->image ?? asset('assets/images/default.png') }}"
                                                                            alt="{{ $item->product->name }}"
                                                                            class="rounded" width="40"
                                                                            height="40">
                                                                        <p class="mb-0 fw-medium">
                                                                            {{ $item->product->name }}
                                                                        </p>
                                                                    </div>
                                                                </td>
                                                                <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                                                </td>
                                                                <td>{{ $item->quantity }}</td>
                                                                <td>Rp
                                                                    {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        <tr>
                                                            <td colspan="3">
                                                                <div class="fw-medium">Total:</div>
                                                            </td>
                                                            <td class="fw-bold">
                                                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- action --}}
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title mb-3">
                                                <i class="bi bi-gear me-2"></i>Action
                                            </h6>
                                            <div class="d-flex align-items-center gap-3">
                                                <a href="{{ route('admin.orders.update-status', ['id' => $order->id, 'status' => 'processing']) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="bi bi-gear"></i> Processing
                                                </a>
                                                <a href="{{ route('admin.orders.update-status', ['id' => $order->id, 'status' => 'shipped']) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="bi bi-truck"></i> Shipped
                                                </a>
                                                <a href="{{ route('admin.orders.update-status', ['id' => $order->id, 'status' => 'completed']) }}"
                                                    class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-circle"></i> Completed
                                                </a>
                                                <a href="{{ route('admin.orders.update-status', ['id' => $order->id, 'status' => 'cancelled']) }}"
                                                    class="btn btn-sm btn-danger">
                                                    <i class="bi bi-x-circle"></i> Cancelled
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- end of modal --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
