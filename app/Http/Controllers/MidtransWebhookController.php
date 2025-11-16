<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransWebhookController extends Controller
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$isProduction = config('midtrans.is_production');
        Config::$serverKey = config('midtrans.server_key');
    }

    public function handle(Request $request)
    {
        try {
            // Create Midtrans notification object
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $orderId = $notification->order_id;
            $fraudStatus = $notification->fraud_status ?? null;
            $transactionId = $notification->transaction_id;
            $paymentType = $notification->payment_type;

            Log::info('Midtrans webhook received', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'transaction_id' => $transactionId,
                'payment_type' => $paymentType
            ]);

            // Find order
            $order = Order::find($orderId);
            if (!$order) {
                Log::error('Order not found', ['order_id' => $orderId]);
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            // Update order status based on transaction status
            $orderStatus = $this->mapTransactionStatus($transactionStatus, $fraudStatus);
            
            if ($orderStatus) {
                $order->update([
                    'status' => $orderStatus,
                    'midtrans_transaction_id' => $transactionId,
                    'midtrans_payment_type' => $paymentType
                ]);

                Log::info('Order status updated', [
                    'order_id' => $orderId,
                    'new_status' => $orderStatus,
                    'transaction_id' => $transactionId
                ]);
            }

            return response()->json(['status' => 'success'], 200);

        } catch (Exception $e) {
            Log::error('Midtrans webhook error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->all()
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function mapTransactionStatus($transactionStatus, $fraudStatus = null)
    {
        switch ($transactionStatus) {
            case 'capture':
                return ($fraudStatus == 'accept') ? 'paid' : 'pending';
            case 'settlement':
                return 'paid';
            case 'pending':
                return 'pending';
            case 'deny':
            case 'cancel':
            case 'expire':
                return 'cancelled';
            case 'failure':
                return 'cancelled';
            default:
                return null;
        }
    }
}
