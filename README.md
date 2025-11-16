# Maulana E-commerce with Midtrans

Aplikasi e-commerce modern yang dibangun dengan Laravel 11, Tailwind CSS, dan Midtrans Payment Gateway. Platform ini menyediakan pengalaman berbelanja yang lengkap dengan fitur manajemen produk, keranjang belanja, dan sistem pembayaran yang aman.

## 🌟 Fitur Utama

- **Autentikasi Pengguna**: Sistem login dan registrasi yang aman
- **Katalog Produk**: Tampilan produk dengan kategori dan pencarian
- **Keranjang Belanja**: Manajemen item belanja yang dinamis
- **Checkout**: Proses checkout yang sederhana dan intuitif
- **Integrasi Midtrans**: Pembayaran yang aman dengan berbagai metode
- **Admin Dashboard**: Panel administrasi untuk mengelola:
  - Produk
  - Kategori
  - Pesanan
  - Pengguna
  - Riwayat Transaksi
- **Order Management**: Pelacakan pesanan dan status pembayaran
- **Responsive Design**: Desain yang responsif untuk semua ukuran layar

## 🛠 Teknologi yang Digunakan

### Backend
- **Laravel 11.9**: Framework PHP modern
- **MySQL**: Database relasional
- **Midtrans PHP SDK 2.6**: Payment gateway integration

### Frontend
- **Tailwind CSS 3.4**: Utility-first CSS framework
- **Alpine.js 3.14**: Lightweight JavaScript framework
- **Flowbite 2.5**: UI component library
- **Vite 5.0**: Next generation frontend tooling

### Development Tools
- **PHP 8.2+**: Bahasa pemrograman
- **Composer**: PHP dependency manager
- **NPM**: JavaScript package manager
- **PHPUnit**: Testing framework
- **Laravel Sail**: Docker development environment

## 📋 Persyaratan Sistem

- PHP 8.2 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Node.js 16 atau lebih tinggi
- Composer
- XAMPP atau Web Server lainnya

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/lana-techn/Maulana-Ecommerce-Midtrans.git
cd "Maulana E-commerce with Midtrans"
```

### 2. Install Dependencies PHP
```bash
composer install
```

### 3. Install Dependencies Node
```bash
npm install
```

### 4. Setup Environment File
```bash
cp .env.example .env
```

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Konfigurasi Database
Edit file `.env` dan sesuaikan konfigurasi database:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_commerce_mid
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Konfigurasi Midtrans
Edit file `.env` dan tambahkan kredensial Midtrans:
```
MIDTRANS_MERCHANT_ID=your-merchant-id
MIDTRANS_CLIENT_KEY=SB-Mid-client-your-client-key
MIDTRANS_SERVER_KEY=SB-Mid-server-your-server-key
MIDTRANS_IS_PRODUCTION=false
```

### 8. Jalankan Migrasi Database
```bash
php artisan migrate
```

### 9. Seed Database (Opsional)
```bash
php artisan db:seed
```

### 10. Build Frontend Assets
```bash
npm run build
```

Atau untuk development dengan hot reload:
```bash
npm run dev
```

### 11. Jalankan Server
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## 📸 Screenshots

### 1. Landing Page
![Landing Page](assets/Screenshot%202025-11-16%20at%2013.58.00.png)

### 2. Product Catalog
![Product Catalog](assets/Screenshot%202025-11-16%20at%2013.58.14.png)

### 3. Shopping Cart
![Shopping Cart](assets/Screenshot%202025-11-16%20at%2013.58.31.png)

### 4. Admin Dashboard
![Admin Dashboard](assets/Screenshot%202025-11-16%20at%2013.58.36.png)

### 5. Order Management
![Order Management](assets/Screenshot%202025-11-16%20at%2013.58.49.png)

## 📁 Struktur Direktori

```
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Semua controller aplikasi
│   │   └── Middleware/       # Middleware custom
│   ├── Models/               # Eloquent models
│   └── Traits/               # Reusable trait classes
├── config/
│   ├── app.php               # Konfigurasi aplikasi
│   ├── database.php          # Konfigurasi database
│   ├── midtrans.php          # Konfigurasi Midtrans
│   └── ...
├── database/
│   ├── migrations/           # Schema migrations
│   ├── seeders/              # Database seeders
│   └── factories/            # Model factories
├── resources/
│   ├── views/                # Blade templates
│   ├── css/                  # CSS files
│   └── js/                   # JavaScript files
├── routes/
│   └── web.php               # Web routes
├── storage/                  # File storage
├── tests/                    # Unit & feature tests
└── vendor/                   # Composer packages
```

## 🔐 Keamanan

- **CSRF Protection**: Token CSRF untuk semua form
- **Authentication**: Sistem autentikasi Laravel yang aman
- **Middleware**: Admin middleware untuk membatasi akses
- **Validasi Input**: Validasi data input di server-side
- **Secure Payment**: Integrasi Midtrans dengan enkripsi end-to-end

## 📊 Database Schema

Aplikasi menggunakan beberapa tabel utama:

- **users**: Data pengguna dan admin
- **categories**: Kategori produk
- **products**: Data produk
- **orders**: Data pesanan
- **order_items**: Item dalam pesanan
- **sessions**: Session management

## 🔄 Workflow Pembayaran

1. Pengguna menambahkan produk ke keranjang
2. Proses checkout dan validasi data
3. Redirect ke Midtrans payment gateway
4. Pengguna melakukan pembayaran
5. Webhook dari Midtrans memperbarui status pesanan
6. Konfirmasi pembayaran dikirim ke pengguna

## 🧪 Testing

Jalankan test suite dengan:
```bash
php artisan test
```

## 📝 Migrasi dan Seeding

### Membuat Migrasi Baru
```bash
php artisan make:migration create_table_name
```

### Menjalankan Migrasi
```bash
php artisan migrate
```

### Membuat Seeder
```bash
php artisan make:seeder SeederName
```

### Menjalankan Seeder
```bash
php artisan db:seed
```

## 🎯 Endpoint API Utama

### Landing Page
- `GET /` - Halaman utama

### User Routes
- `GET /login` - Form login
- `POST /login` - Proses login
- `GET /register` - Form registrasi
- `POST /register` - Proses registrasi
- `GET /dashboard` - User dashboard
- `GET /products` - Daftar produk
- `POST /cart/add` - Tambah ke keranjang
- `GET /cart` - Lihat keranjang

### Checkout & Payment
- `GET /checkout` - Form checkout
- `POST /checkout` - Proses checkout
- `GET /orders` - Riwayat pesanan

### Admin Routes
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/products` - Kelola produk
- `GET /admin/categories` - Kelola kategori
- `GET /admin/orders` - Kelola pesanan
- `GET /admin/users` - Kelola pengguna

### Webhook
- `POST /webhook/midtrans` - Midtrans webhook

## 📞 Dukungan Midtrans

Untuk integrasi Midtrans, kunjungi:
- [Midtrans Dashboard](https://app.sandbox.midtrans.com)
- [Dokumentasi Midtrans](https://docs.midtrans.com)
- [PHP SDK Documentation](https://github.com/Midtrans/midtrans-php)

## 📄 File Penting

- `composer.json` - PHP dependencies
- `package.json` - JavaScript dependencies
- `.env.example` - Template environment variables
- `phpunit.xml` - PHPUnit configuration
- `tailwind.config.js` - Tailwind CSS configuration
- `vite.config.js` - Vite configuration

## 🤝 Kontribusi

Silakan fork repository ini dan membuat pull request untuk kontribusi.

## 📄 Lisensi

Proyek ini dilisensikan di bawah MIT License - lihat file LICENSE untuk detail.

## 👥 Author

**Maulana E-commerce Project**
- GitHub: [@lana-techn](https://github.com/lana-techn)

## 🐛 Bug Reporting

Jika menemukan bug, silakan buat issue di GitHub repository ini dengan detail yang jelas.

## 📞 Kontak

Untuk pertanyaan atau saran, silakan hubungi melalui GitHub Issues.

---

**Terakhir diperbarui**: November 16, 2025
