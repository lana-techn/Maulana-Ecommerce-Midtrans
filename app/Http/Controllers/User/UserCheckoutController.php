<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Exception;
use Illuminate\Http\Request;
use Midtrans\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserCheckoutController extends Controller
{

    public function __construct()
    {
        try {
            
            $serverKey = config('midtrans.server_key');
            if (empty($serverKey)) {
                throw new Exception('Please define your midtrans server key');
            }
            Config::$serverKey = $serverKey;
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = config('midtrans.is_sanitized');
            Config::$is3ds = config('midtrans.enable_3ds');
        } catch (Exception $e) {
            Log::error('Midtrans Config error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function process(Request $request): JsonResponse
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'User not authenticated'
                ], 401);
            }

            $validated = $request->validate([
                'name' => ['required', 'string'],
                'phone' => ['required', 'string'],
                'shipping_address' => ['required', 'string'],
                'notes' => ['required', 'string'],
                'cart' => ['required', 'array'],
            ]);

            DB::beginTransaction();

            Log::info('Checkout process started', ['user_id' => auth()->id()]);

            $order = Order::create([
                'user_id' => auth()->id(),
                'shipping_address' => $request->shipping_address,
                'total_amount' => 0,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            $totalAmount = 0;
            $items = [];

            foreach ($request->cart as $item) {
                if (!isset($item['id'], $item['quantity'], $item['price'], $item['name'])) {
                    throw new Exception('Invalid cart item');
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                $totalAmount += $item['price'] * $item['quantity'];

                $items[] = [
                    'id' => (string) $item['id'],
                    'price' => (int) $item['price'],
                    'quantity' => (int) $item['quantity'],
                    'name' => $item['name'],
                ];
            }

            $shippingCost = 20000;
            $totalAmount += $shippingCost;

            $order->update(['total_amount' => $totalAmount]);

            $params = [
                'transaction_details' => [
                    'order_id' => (string) $order->id,
                    'gross_amount' => (int) $totalAmount,
                ],
                'item_details' => array_merge($items, [
                    [
                        'id' => 'shipping',
                        'price' => $shippingCost,
                        'quantity' => 1,
                        'name' => 'Shipping Cost',
                    ]
                ]),
                'customer_details' => [
                    'first_name' => $request->name,
                    'email' => auth()->user()->email,
                    'phone' => $request->phone,
                    'billing_address' => [
                        'address' => $request->shipping_address
                    ],
                    'shipping_address' => [
                        'address' => $request->shipping_address
                    ]
                ]
            ];

            Log::info('Midtrans parameters prepared', ['params' => $params]);

            $snapToken = Snap::getSnapToken($params);
            if (empty($snapToken)) {
                Log::error('Failed to retrieve Snap Token', ['params' => $params]);
                throw new Exception('Snap Token is empty');
            }

            Log::info('Snap Token generated successfully', [
                'order_id' => $order->id,
                'snap_token' => $snapToken,
            ]);

            $order->update(['snap_token' => $snapToken]);

            DB::commit();

            Log::info('Order successfully created', [
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'total_amount' => $totalAmount,
            ]);

            return response()->json([
                'status' => 'success',
                'snap_token' => $snapToken,
                'order_id' => $order->id,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Checkout process error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id() ?? null,
            ]);

            return response()->json([
                'status' => 'failed',
                'message' => 'Error in payment process: ' . $e->getMessage(),
            ], 442);
        }
    }



    public function updateStatus(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required',
                'transaction_id' => 'required',
                'payment_type' => 'required',
                'status' => 'required|in:paid,pending,cancelled',
            ]);
            $order = Order::findOrFail( $request->order_id);

            if($order->user_id !== auth()->id()) {
                throw new Exception('Unauthorized access');

            }

            $order->update([
                'status' => $request->status,
                'midtrans_transaction_id' => $request->transaction_id,
                'midtrans_payment_type' => $request->payment_type
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Order status updated successfully',
            ]);
        }catch (Exception $e) {
           Log::error( 'Error updating order status: ' . $e->getMessage());

           return response()->json([
                'status' => 'failed',
                'message' => 'Error updating order status: ' . $e->getMessage(),
           ], 422);
        }
    }
}
