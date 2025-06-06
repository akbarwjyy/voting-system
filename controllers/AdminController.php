<?php
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Admin.php';
require_once '../models/Vote.php';
require_once '../models/Candidate.php';

class AdminController
{
    private $db;
    private $user;
    private $admin;
    private $vote;
    private $candidate;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getKoneksi();
        $this->user = new User($this->db);
        $this->admin = new Admin($this->db);
        $this->vote = new Vote($this->db);
        $this->candidate = new Candidate($this->db);
    }

    // Mendapatkan daftar semua user
    public function getDaftarUser()
    {
        $stmt = $this->user->ambilSemuaUser();
        return [
            'sukses' => true,
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    // Mengaktifkan atau menonaktifkan user
    public function toggleStatusUser($user_id, $status)
    {
        $query = "UPDATE users SET is_active = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $user_id);

        if ($stmt->execute()) {
            return [
                'sukses' => true,
                'pesan' => 'Status user berhasil diperbarui!'
            ];
        }
        return [
            'sukses' => false,
            'pesan' => 'Gagal memperbarui status user!'
        ];
    }

    // Menghapus user
    public function hapusUser($user_id)
    {
        // Cek apakah user sudah memilih
        $query_cek = "SELECT sudah_memilih FROM users WHERE id = :id";
        $stmt_cek = $this->db->prepare($query_cek);
        $stmt_cek->bindParam(':id', $user_id);
        $stmt_cek->execute();
        $user = $stmt_cek->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['sudah_memilih']) {
            return [
                'sukses' => false,
                'pesan' => 'Tidak dapat menghapus user yang sudah melakukan voting!'
            ];
        }

        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $user_id);

        if ($stmt->execute()) {
            return [
                'sukses' => true,
                'pesan' => 'User berhasil dihapus!'
            ];
        }
        return [
            'sukses' => false,
            'pesan' => 'Gagal menghapus user!'
        ];
    }

    // Ubah password admin
    public function ubahPasswordAdmin($admin_id, $password_lama, $password_baru, $konfirmasi_password)
    {
        // Validasi password baru
        if (strlen($password_baru) < 6) {
            return [
                'sukses' => false,
                'pesan' => 'Password baru minimal 6 karakter!'
            ];
        }

        if ($password_baru !== $konfirmasi_password) {
            return [
                'sukses' => false,
                'pesan' => 'Konfirmasi password tidak sesuai!'
            ];
        }

        // Verifikasi password lama
        $query = "SELECT password FROM admins WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $admin_id);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($password_lama, $admin['password'])) {
            return [
                'sukses' => false,
                'pesan' => 'Password lama tidak sesuai!'
            ];
        }

        // Update password
        if ($this->admin->ubahPassword($admin_id, $password_baru)) {
            return [
                'sukses' => true,
                'pesan' => 'Password berhasil diubah!'
            ];
        }
        return [
            'sukses' => false,
            'pesan' => 'Gagal mengubah password!'
        ];
    }

    // Mendapatkan statistik untuk dashboard
    public function getStatistikDashboard()
    {
        // Total user
        $query_users = "SELECT 
            COUNT(*) as total_user,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as total_aktif,
            SUM(CASE WHEN sudah_memilih = 1 THEN 1 ELSE 0 END) as total_voted
            FROM users";
        $stmt_users = $this->db->query($query_users);
        $stats_users = $stmt_users->fetch(PDO::FETCH_ASSOC);

        // Total kandidat
        $query_kandidat = "SELECT COUNT(*) as total_kandidat FROM candidates";
        $stmt_kandidat = $this->db->query($query_kandidat);
        $stats_kandidat = $stmt_kandidat->fetch(PDO::FETCH_ASSOC);

        // Statistik voting
        $persentase_aktif = $stats_users['total_user'] > 0 ?
            round(($stats_users['total_aktif'] / $stats_users['total_user']) * 100, 2) : 0;

        $persentase_voted = $stats_users['total_aktif'] > 0 ?
            round(($stats_users['total_voted'] / $stats_users['total_aktif']) * 100, 2) : 0;

        return [
            'sukses' => true,
            'data' => [
                'total_user' => $stats_users['total_user'],
                'total_aktif' => $stats_users['total_aktif'],
                'total_voted' => $stats_users['total_voted'],
                'total_kandidat' => $stats_kandidat['total_kandidat'],
                'persentase_aktif' => $persentase_aktif,
                'persentase_voted' => $persentase_voted
            ]
        ];
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $adminController = new AdminController();
    $hasil = [];

    switch ($_POST['action']) {
        case 'get_users':
            $hasil = $adminController->getDaftarUser();
            break;

        case 'toggle_status':
            if (isset($_POST['user_id'], $_POST['status'])) {
                $hasil = $adminController->toggleStatusUser(
                    $_POST['user_id'],
                    $_POST['status'] === 'true'
                );
            }
            break;

        case 'hapus_user':
            if (isset($_POST['user_id'])) {
                $hasil = $adminController->hapusUser($_POST['user_id']);
            }
            break;

        case 'ubah_password':
            if (isset(
                $_POST['admin_id'],
                $_POST['password_lama'],
                $_POST['password_baru'],
                $_POST['konfirmasi_password']
            )) {
                $hasil = $adminController->ubahPasswordAdmin(
                    $_POST['admin_id'],
                    $_POST['password_lama'],
                    $_POST['password_baru'],
                    $_POST['konfirmasi_password']
                );
            }
            break;

        case 'get_statistik':
            $hasil = $adminController->getStatistikDashboard();
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
