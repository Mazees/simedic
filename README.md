# SIMEDIC — Sistem Manajemen Obat Apotek

Aplikasi web manajemen apotek berbasis PHP native. Mencakup autentikasi berbasis session, manajemen stok obat, kasir POS, histori transaksi, dan manajemen pengguna dengan sistem role dua tingkat.

---

## Fitur

| Modul | Deskripsi | Akses |
|---|---|---|
| **Login** | Autentikasi username + password dengan `password_hash` | Semua |
| **Dashboard** | Ringkasan omzet, transaksi, item kritis, dan grafik penjualan 7 hari | User, Super Admin |
| **Kasir (POS)** | Pilih obat, keranjang belanja, hitung PPN 11%, checkout | User, Super Admin |
| **Data Produk** | Tambah, edit, dan hapus master data obat/produk | User, Super Admin |
| **Stok Obat** | Daftar stok, tambah/kurangi unit, tambah obat baru, riwayat penyesuaian | User, Super Admin |
| **Histori Transaksi** | Rekap transaksi yang telah selesai | User, Super Admin |
| **Manajemen User** | Tambah user, hapus user, promote/demote ke Super Admin | **Super Admin only** |

---

## Teknologi

- **Backend:** PHP native (tanpa framework)
- **Database:** MySQL via `mysqli`
- **Frontend:** [Tailwind CSS](https://tailwindcss.com/) (via CDN) + [Alpine.js](https://alpinejs.dev/) (via CDN)
- **Font:** [Space Grotesk](https://fonts.google.com/specimen/Space+Grotesk) (Google Fonts)
- **Server lokal:** [Laragon](https://laragon.org/)

---

## Struktur Direktori

```
simedic/
├── models/           # Class PHP — logika bisnis dan akses database
├── components/       # Komponen UI reusable (sidebar, header)
├── migration/        # Skema SQL untuk setup database awal
├── login/            # Halaman autentikasi
├── dashboard/        # Halaman ringkasan operasional
├── list-product/     # Manajemen data produk (tambah, edit, hapus)
├── stok-obat/        # Manajemen stok dan inventaris obat
├── pos-obat/         # Kasir / Point of Sale
├── invoice/          # Halaman struk / bukti transaksi
├── histori-transaksi/# Rekap transaksi yang telah selesai
├── manajemen-user/   # Manajemen akun pengguna (Super Admin only)
└── error/            # Halaman error (400/401/403/404/500)
```

---

## Skema Database

```sql
-- Akun pengguna sistem
CREATE TABLE users (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    username       VARCHAR(50),
    password       VARCHAR(255),       -- bcrypt via password_hash()
    is_super_admin TINYINT(1) DEFAULT 0
);

-- Master data obat
CREATE TABLE product (
    id    INT PRIMARY KEY AUTO_INCREMENT,
    nama  VARCHAR(100),
    harga INT
);

-- Stok per batch
create table stok (
 id INT primary key auto_increment,
 id_product INT,
 batch VARCHAR(100) NOT NULL UNIQUE,
 jumlah INT DEFAULT 0,
 tgl_masuk DATE DEFAULT (CURRENT_DATE),
 tgl_exp DATE,
 foreign key (id_product) references product (id)
);

-- Header transaksi
CREATE TABLE transaksi (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    total_harga    INT,
    tgl_pembelian  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Detail item per transaksi
CREATE TABLE detail_transaksi (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    id_transaksi   INT,
    id_product     INT,
    nama_product   VARCHAR(100),
    harga_product  INT,
    qty            INT,
    subtotal       INT GENERATED ALWAYS AS (harga_product * qty) STORED,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (id_product)   REFERENCES product(id)   ON DELETE SET NULL
);
```

---

## Instalasi

### Prasyarat
- [Laragon](https://laragon.org/) (atau XAMPP/WAMP)
- PHP >= 8.0
- MySQL >= 5.7

### Langkah-langkah

1. **Clone / salin project ke direktori web Laragon:**
   ```
   C:\laragon\www\simedic\
   ```

2. **Buat database di MySQL:**
   ```sql
   CREATE DATABASE simedic;
   CREATE USER 'simedic'@'localhost' IDENTIFIED BY 'simedicdb26';
   GRANT ALL PRIVILEGES ON simedic.* TO 'simedic'@'localhost';
   ```

3. **Jalankan migration:**
   Buka file `migration/simedic-1.sql` dan jalankan di phpMyAdmin atau MySQL CLI:
   ```bash
   mysql -u simedic -p simedic < migration/simedic-1.sql
   ```

4. **Seed akun Super Admin:**
   Buka `createadmin.php`, uncomment semua baris, lalu akses sekali via browser:
   ```
   http://localhost/simedic/createadmin.php
   ```
   > ⚠️ Setelah berhasil, comment kembali atau hapus isi file tersebut.

   Akun default yang di-seed:
   | Username | Password |
   |---|---|
   | `admin` | `obat123` |

5. **Akses aplikasi:**
   ```
   http://localhost/simedic/
   ```

---

## Sistem Role & Akses

Aplikasi menggunakan dua role yang diimplementasikan via OOP inheritance:

```
Database
  └── User           → login, logout, canAccess()
        └── SuperAdmin → + addUser(), deleteUser(),
                          changeUserToSuperAdmin(), changeUserToUser(),
                          getAllUsers(), canAccess() (override)
```

### Tabel Hak Akses

| Halaman | User | Super Admin |
|---|:---:|:---:|
| Dashboard | ✅ | ✅ |
| Kasir (POS) | ✅ | ✅ |
| Data Produk | ✅ | ✅ |
| Stok Obat | ✅ | ✅ |
| Histori Transaksi | ✅ | ✅ |
| Manajemen User | ❌ | ✅ |

Seluruh halaman memvalidasi akses di baris pertama:
```php
if (!$user->canAccess('nama-halaman')) {
    header('Location: /simedic/error?code=403');
    exit;
}
```

---

## Pola Kode

### PHP
- Satu file `index.php` per halaman — handle POST di bagian atas, render HTML di bawah
- Variabel `$user` diinisialisasi global di `models/user.php` (coba `SuperAdmin`, fallback ke `User`)
- Redirect setelah POST (PRG pattern) untuk mencegah duplicate submit
- Proteksi diri sendiri di manajemen user: `$u['id'] != $_SESSION['user_id']`

### HTML / Frontend
- Layout sidebar + main dengan CSS Grid: `lg:grid-cols-[260px_1fr]`
- State management ringan via Alpine.js (`x-data`, `x-model`, `x-for`, `@click`)
- Tailwind CSS dikonfigurasi inline dengan color token `brand` (cyan-based)
- Komponen reusable: `sidebar.php` dan `header.php` di-include dari setiap halaman

---

## Konfigurasi Database

Kredensial ada di `config.php`:

```php
private $host = "localhost";
private $user = "simedic";
private $pass = "simedicdb26";
private $db   = "simedic";
```

> Untuk production, pindahkan kredensial ke environment variable atau file `.env`.

---

## Halaman Error

Halaman `/simedic/error` menerima parameter `?code=` dan mendukung kode:

| Kode | Pesan | Warna Aksen |
|---|---|---|
| 400 | Permintaan Tidak Valid | Ungu |
| 401 | Akses Perlu Login | Kuning |
| 403 | Akses Ditolak | Merah |
| 404 | Halaman Tidak Ditemukan | Indigo |
| 500 | Terjadi Kesalahan Server | Merah |
