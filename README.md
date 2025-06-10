# 🗳️ E-Voting - Sistem Pemilihan Online

Aplikasi E-Voting berbasis web untuk pemilihan. Dibangun menggunakan PHP dan MySQL dengan tampilan modern menggunakan Tailwind CSS.

## 🚀 Fitur Utama

### Untuk Pemilih

- Login dan registrasi akun pemilih
- Lihat profil dan visi-misi kandidat
- Melakukan voting secara aman dan rahasia
- Melihat hasil voting (setelah periode voting selesai)
- Antarmuka responsif dan user-friendly

### Untuk Admin

- Dashboard admin dengan statistik real-time
- Manajemen akun pemilih (aktivasi/deaktivasi)
- Manajemen data kandidat
- Pengaturan periode voting
- Monitoring status voting dan partisipasi

1. Clone repository ini ke direktori web server Anda:

```bash
git clone https://github.com/akbarwjyy/voting-system.git
```

2. Import database menggunakan file SQL yang disediakan

3. Konfigurasi database di `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'voting_system');
```

4. Akses aplikasi melalui browser:

```
http://localhost/voting-system
```

## 📁 Struktur Project

```
voting-system/
├── admin.php              # Halaman login admin
├── index.php             # Halaman utama
├── assets/               # Assets statis (CSS, uploads)
├── config/               # Konfigurasi aplikasi
├── controllers/          # Controllers
├── models/              # Model data
├── views/               # View templates
│   ├── admin/          # Halaman admin
│   ├── auth/           # Halaman autentikasi
│   ├── includes/       # Komponen yang dapat digunakan kembali
│   └── user/           # Halaman user
└── voting/             # Modul voting
```

## 👥 Role Pengguna

### Admin

- Username: admin
- Akses ke panel admin
- Manajemen sistem voting

### Pemilih

- Registrasi menggunakan email
- Perlu aktivasi oleh admin
- Hak voting satu kali

## 🔒 Keamanan

- Autentikasi multi-level (admin/pemilih)
- Validasi input form
- Proteksi session
- Pencegahan multiple voting
- Password hashing

## 📱 Responsive Design

Aplikasi dioptimalkan untuk berbagai ukuran layar:

- Desktop
- Tablet
- Mobile

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan fork repository ini dan ajukan pull request.

## 📝 Lisensi

[MIT License](LICENSE)
