# Implementasi Payment Gateway Midtrans

## Overview
Proyek e-commerce ini telah terintegrasi dengan payment gateway Midtrans untuk memproses pembayaran online yang aman dan terpercaya.

## Fitur Midtrans yang Diimplementasikan

### 1. **Payment Processing** 
- ✅ Checkout dengan Midtrans Snap
- ✅ Support berbagai metode pembayaran (Bank Transfer, Credit Card, E-wallet, dll)
- ✅ Real-time payment status updates
- ✅ Automatic order status management

### 2. **Webhook Integration**
- ✅ Automatic payment notification handling
- ✅ Real-time order status updates
- ✅ Fraud detection integration
- ✅ Transaction logging

### 3. **Order Management**
- ✅ User order history
- ✅ Order status tracking
- ✅ Cancel pending orders
- ✅ Re-payment for failed orders

## Setup Konfigurasi

### 1. Environment Variables
Tambahkan konfigurasi Midtrans pada file `.env`:

```env
# Midtrans Configuration
MIDTRANS_MERCHANT_ID=your-merchant-id
MIDTRANS_CLIENT_KEY=SB-Mid-client-your-client-key
MIDTRANS_SERVER_KEY=SB-Mid-server-your-server-key
MIDTRANS_IS_PRODUCTION=false
```

**Untuk Sandbox (Testing):**
- `MIDTRANS_IS_PRODUCTION=false`
- Client Key: `SB-Mid-client-xxxxxxxxx`
- Server Key: `SB-Mid-server-xxxxxxxxx`

**Untuk Production:**
- `MIDTRANS_IS_PRODUCTION=true`
- Client Key: `Mid-client-xxxxxxxxx`
- Server Key: `Mid-server-xxxxxxxxx`

### 2. Database Migration
Jalankan migration untuk menambahkan field notes:

```bash
php artisan migrate
```

### 3. Webhook Configuration
Konfigurasi webhook URL di Midtrans Dashboard:
- **Notification URL:** `https://yourdomain.com/midtrans/webhook`
- **Finish URL:** `https://yourdomain.com/user/orders`
- **Unfinish URL:** `https://yourdomain.com/cart`
- **Error URL:** `https://yourdomain.com/cart`

## Struktur File yang Ditambahkan/Dimodifikasi

### 1. **Controllers**
```
app/Http/Controllers/
├── MidtransWebhookController.php          # Webhook handler
├── User/UserCheckoutController.php        # Existing (enhanced)
└── User/UserOrderController.php           # Order management
```

### 2. **Models**
```
app/Models/Order.php                       # Enhanced with notes field
```

### 3. **Views**
```
resources/views/user/orders/
└── index.blade.php                        # Order history page
```

### 4. **JavaScript**
```
resources/js/cart.js                       # Enhanced with payment integration
```

### 5. **Routes**
```
routes/web.php                             # Added webhook and user order routes
```

### 6. **Middleware**
```
app/Http/Middleware/VerifyCsrfToken.php    # CSRF exemption for webhook
```

## Flow Pembayaran

### 1. **Checkout Process**
1. User memilih produk dan menambahkan ke cart
2. User mengisi informasi shipping di halaman cart
3. User klik "Pay Now"
4. Sistem membuat order dan mendapatkan Snap Token dari Midtrans
5. Snap popup terbuka dengan pilihan metode pembayaran
6. User memilih dan menyelesaikan pembayaran

### 2. **Payment Status Handling**
- **Success:** Order status → 'paid', redirect ke order history
- **Pending:** Order status → 'pending', redirect ke order history
- **Failed:** Order status tetap 'pending', user bisa retry payment
- **Cancel:** User kembali ke checkout form

### 3. **Webhook Processing**
- Midtrans mengirim notifikasi ke `/midtrans/webhook`
- Sistem memvalidasi dan update status order otomatis
- Log semua transaksi untuk audit trail

## Status Order yang Didukung

| Status | Deskripsi |
|--------|-----------|
| `pending` | Order dibuat, menunggu pembayaran |
| `paid` | Pembayaran berhasil |
| `processing` | Order sedang diproses |
| `shipped` | Order sudah dikirim |
| `delivered` | Order sudah diterima |
| `cancelled` | Order dibatalkan |

## Fitur User Interface

### 1. **Shopping Cart**
- Real-time cart updates
- Shipping form integration
- Payment processing dengan loading states
- Error handling dan notifikasi

### 2. **Order History**
- List semua order user
- Filter berdasarkan status
- Detail order dengan item breakdown
- Re-payment untuk pending orders
- Cancel pending orders

### 3. **Navigation**
- Link "My Orders" di header (untuk user yang login)
- Cart count indicator
- User dropdown dengan logout option

## Testing

### 1. **Test Cards untuk Sandbox**
```
Credit Card (Success): 4811 1111 1111 1114
Credit Card (Failure): 4911 1111 1111 1113
CVV: 123
Exp: Any future date
```

### 2. **Bank Transfer**
- Mandiri VA: Otomatis generate VA number
- BCA VA: Otomatis generate VA number
- BNI VA: Otomatis generate VA number

### 3. **E-Wallet**
- GoPay: Scan QR code di simulator
- ShopeePay: Login dengan test account

## Security Features

1. **CSRF Protection:** Webhook endpoint dikecualikan dari CSRF
2. **Signature Verification:** Webhook menggunakan server key untuk validasi
3. **User Authorization:** User hanya bisa akses order miliknya
4. **SQL Injection Prevention:** Menggunakan Eloquent ORM
5. **XSS Protection:** Output di-escape oleh Blade template

## Error Handling

### 1. **Payment Errors**
- Network timeout
- Invalid payment data
- Insufficient balance
- Card declined

### 2. **System Errors**
- Database connection issues
- Midtrans API errors
- Invalid webhook data
- Authentication failures

## Monitoring & Logging

Semua aktivitas payment di-log untuk monitoring:
- Payment success/failure
- Webhook notifications
- Order status changes
- Error occurrences

Log dapat dilihat di `storage/logs/laravel.log`

## Production Checklist

- [ ] Update environment variables dengan production keys
- [ ] Konfigurasi webhook URLs di Midtrans Dashboard
- [ ] Enable HTTPS untuk semua endpoints
- [ ] Setup monitoring untuk webhook failures
- [ ] Configure proper error logging
- [ ] Test semua payment methods
- [ ] Verify webhook security

## Support & Documentation

- **Midtrans Docs:** https://docs.midtrans.com
- **Snap Integration:** https://docs.midtrans.com/en/snap/overview
- **API Reference:** https://api-docs.midtrans.com

## Troubleshooting

### Common Issues:
1. **Webhook not receiving:** Check URL configuration and firewall
2. **Payment failed:** Verify server key and environment
3. **Order not updating:** Check webhook logs and database connection
4. **JS errors:** Ensure Snap.js is loaded correctly

Untuk bantuan lebih lanjut, hubungi tim development atau check Midtrans support.
