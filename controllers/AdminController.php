<?php
// Include constants file first
require_once __DIR__ . '/../config/constants.php';

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Admin.php';
require_once BASE_PATH . '/models/Vote.php';
require_once BASE_PATH . '/models/Candidate.php';

class AdminController
{
    private $db;
    private $user;
    private $admin;
    private $vote;
    private $candidate;
    private $votingSetting;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getKoneksi();
        $this->user = new User($this->db);
        $this->admin = new Admin($this->db);
        $this->vote = new Vote($this->db);
        $this->candidate = new Candidate($this->db);

        // Initialize VotingSetting
        require_once BASE_PATH . '/models/VotingSetting.php';
        $this->votingSetting = new VotingSetting($this->db);
    }

    // Mendapatkan daftar semua user
    public function getDaftarUser()
    {
        $query = "SELECT id, nama, email, is_active, sudah_memilih FROM users ORDER BY tanggal_daftar DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Aktivasi akun user
    public function aktivasiUser($user_id)
    {
        $query = "UPDATE users SET is_active = 1 WHERE id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }

    // Deaktivasi akun user
    public function deaktivasiUser($user_id)
    {
        $query = "UPDATE users SET is_active = 0 WHERE id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
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

    // Mengambil statistik untuk dashboard admin
    public function getStatistikDashboard()
    {
        $query = "SELECT 
            (SELECT COUNT(*) FROM users) as total_user,
            (SELECT COUNT(*) FROM users WHERE is_active = 1) as total_aktif,
            (SELECT COUNT(*) FROM users WHERE sudah_memilih = 1) as total_voted,
            (SELECT COUNT(*) FROM candidates) as total_kandidat";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Hitung persentase partisipasi
        $persentase_voted = ($result['total_aktif'] > 0)
            ? round(($result['total_voted'] / $result['total_aktif']) * 100)
            : 0;

        return [
            'sukses' => true,
            'data' => array_merge($result, ['persentase_voted' => $persentase_voted])
        ];
    }

    // Method untuk mendapatkan status voting
    public function getStatusVoting()
    {
        $status = $this->votingSetting->getStatus();
        return ['status' => $status];
    }

    // Method untuk mengupdate pengaturan voting
    public function updateStatusVoting($data)
    {
        // Validasi input
        if (empty($data['waktu_mulai']) || empty($data['waktu_selesai'])) {
            return [
                'sukses' => false,
                'pesan' => 'Waktu mulai dan waktu selesai harus diisi'
            ];
        }

        // Validasi waktu
        $waktuMulai = new DateTime($data['waktu_mulai']);
        $waktuSelesai = new DateTime($data['waktu_selesai']);

        if ($waktuSelesai <= $waktuMulai) {
            return [
                'sukses' => false,
                'pesan' => 'Waktu selesai harus lebih besar dari waktu mulai'
            ];
        }

        // Update pengaturan
        $result = $this->votingSetting->updateStatus([
            'waktu_mulai' => $data['waktu_mulai'],
            'waktu_selesai' => $data['waktu_selesai'],
            'judul_voting' => $data['judul_voting'] ?? 'Pemilihan Ketua'
        ]);

        if ($result['sukses']) {
            $status = $this->votingSetting->getStatus();
            $pesanStatus = $status['voting_aktif'] ?
                'Voting akan aktif selama periode yang ditentukan.' :
                'Voting akan aktif saat memasuki waktu yang ditentukan.';

            return [
                'sukses' => true,
                'pesan' => 'Pengaturan voting berhasil diperbarui. ' . $pesanStatus
            ];
        }

        return $result;
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

        case 'get_status_voting':
            $hasil = $adminController->getStatusVoting();
            break;

        case 'update_status_voting':
            if (isset($_POST['status'])) {
                $hasil = $adminController->updateStatusVoting($_POST['status'] === 'true');
            }
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
