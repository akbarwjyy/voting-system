<?php
// Include constants file first
require_once __DIR__ . '/../config/constants.php';

// Include files dengan path absolut
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/models/User.php';
require_once ROOT_PATH . '/models/Admin.php';

class AuthController
{
    // Helper method untuk mengelola session
    private static function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private $db;
    private $user;
    private $admin;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getKoneksi();
        $this->user = new User($this->db);
        $this->admin = new Admin($this->db);
    }

    // Proses registrasi user
    public function prosesRegistrasi($nama, $email, $password, $konfirmasi_password)
    {
        // Validasi input
        if (empty($nama) || empty($email) || empty($password) || empty($konfirmasi_password)) {
            return [
                'sukses' => false,
                'pesan' => 'Semua field harus diisi!'
            ];
        }

        // Validasi email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'sukses' => false,
                'pesan' => 'Format email tidak valid!'
            ];
        }

        // Validasi password
        if (strlen($password) < 6) {
            return [
                'sukses' => false,
                'pesan' => 'Password minimal 6 karakter!'
            ];
        }

        if ($password !== $konfirmasi_password) {
            return [
                'sukses' => false,
                'pesan' => 'Konfirmasi password tidak cocok!'
            ];
        }

        // Set data user
        $this->user->nama = $nama;
        $this->user->email = $email;
        $this->user->password = $password;

        // Cek email sudah terdaftar
        if ($this->user->cekEmailSudahAda()) {
            return [
                'sukses' => false,
                'pesan' => 'Email sudah terdaftar!'
            ];
        }

        // Daftar user
        if ($this->user->daftarUser()) {
            return [
                'sukses' => true,
                'pesan' => 'Registrasi berhasil! Menunggu aktivasi dari admin.'
            ];
        } else {
            return [
                'sukses' => false,
                'pesan' => 'Registrasi gagal! Silakan coba lagi.'
            ];
        }
    }

    // Proses login user
    public function prosesLoginUser($email, $password)
    {
        // Validasi input
        if (empty($email) || empty($password)) {
            return [
                'sukses' => false,
                'pesan' => 'Email dan password harus diisi!'
            ];
        }

        // Set data untuk login
        $this->user->email = $email;
        $this->user->password = $password;

        // Proses login
        if ($this->user->masukUser()) {
            // Set session
            self::startSession();
            $_SESSION['user_id'] = $this->user->id;
            $_SESSION['user_nama'] = $this->user->nama;
            $_SESSION['user_email'] = $this->user->email;
            $_SESSION['user_type'] = 'user';
            $_SESSION['sudah_memilih'] = $this->user->sudah_memilih;

            return [
                'sukses' => true,
                'pesan' => 'Login berhasil!',
                'redirect' => '../user/dashboard.php'
            ];
        } else {
            return [
                'sukses' => false,
                'pesan' => 'Email atau password salah, atau akun belum diaktifkan!'
            ];
        }
    }

    // Proses login admin
    public function prosesLoginAdmin($username, $password)
    {
        // Validasi input
        if (empty($username) || empty($password)) {
            return [
                'sukses' => false,
                'pesan' => 'Username dan password harus diisi!'
            ];
        }

        // Set data untuk login
        $this->admin->username = $username;
        $this->admin->password = $password;

        // Proses login
        if ($this->admin->masukAdmin()) {
            // Set session
            self::startSession();
            $_SESSION['admin_id'] = $this->admin->id;
            $_SESSION['admin_username'] = $this->admin->username;
            $_SESSION['user_type'] = 'admin';

            return [
                'sukses' => true,
                'pesan' => 'Login admin berhasil!',
                'redirect' => '../admin/dashboard.php'
            ];
        } else {
            return [
                'sukses' => false,
                'pesan' => 'Username atau password admin salah!'
            ];
        }
    }

    // Proses logout
    public function prosesLogout()
    {
        self::startSession();
        session_unset();
        session_destroy();

        header("Location: ../index.php");
        exit();
    }

    // Cek apakah user sudah login
    public static function cekLoginUser()
    {
        self::startSession();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'user') {
            header("Location: ../auth/login.php?type=user");
            exit();
        }
    }

    // Cek apakah admin sudah login
    public static function cekLoginAdmin()
    {
        self::startSession();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            header("Location: ../auth/login.php?type=admin");
            exit();
        }
    }

    // Cek apakah sudah login (redirect ke dashboard masing-masing)
    public static function cekSudahLogin()
    {
        self::startSession();
        if (isset($_SESSION['user_type'])) {
            if ($_SESSION['user_type'] === 'admin') {
                header("Location: views/admin/dashboard.php");
                exit();
            } else if ($_SESSION['user_type'] === 'user') {
                header("Location: views/user/dashboard.php");
                exit();
            }
        }
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController();

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'registrasi':
                $hasil = $auth->prosesRegistrasi(
                    $_POST['nama'],
                    $_POST['email'],
                    $_POST['password'],
                    $_POST['konfirmasi_password']
                );
                break;

            case 'login_user':
                $hasil = $auth->prosesLoginUser(
                    $_POST['email'],
                    $_POST['password']
                );
                break;

            case 'login_admin':
                $hasil = $auth->prosesLoginAdmin(
                    $_POST['username'],
                    $_POST['password']
                );
                break;

            case 'logout':
                $auth->prosesLogout();
                break;

            default:
                $hasil = [
                    'sukses' => false,
                    'pesan' => 'Action tidak valid!'
                ];
        }

        // Return JSON response untuk AJAX
        header('Content-Type: application/json');
        echo json_encode($hasil);
    }
}
