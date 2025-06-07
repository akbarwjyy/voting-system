<?php
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../models/Candidate.php');
require_once(__DIR__ . '/../models/Vote.php');

class CandidateController
{
    private $db;
    private $candidate;
    private $vote;
    private $upload_dir;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getKoneksi();
        $this->candidate = new Candidate($this->db);
        $this->vote = new Vote($this->db);
        $this->upload_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'kandidat' . DIRECTORY_SEPARATOR;

        // Create upload directory if it doesn't exist
        if (!file_exists($this->upload_dir)) {
            mkdir($this->upload_dir, 0777, true);
        }
    }

    // Mendapatkan semua kandidat
    public function ambilSemuaKandidat()
    {
        $stmt = $this->candidate->ambilSemuaKandidat();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mendapatkan detail kandidat
    public function ambilKandidatById($id)
    {
        return $this->candidate->ambilKandidatById($id);
    }

    // Menambah kandidat baru
    public function tambahKandidat($data, $foto)
    {
        // Validasi input
        if (empty($data['nama']) || empty($data['visi']) || empty($data['misi']) || empty($data['no_urut'])) {
            return [
                'sukses' => false,
                'pesan' => 'Semua field harus diisi!'
            ];
        }

        // Validasi no_urut unik
        if ($this->candidate->cekNomorUrut($data['no_urut'])) {
            return [
                'sukses' => false,
                'pesan' => 'Nomor urut sudah digunakan!'
            ];
        }

        // Handle upload foto
        $foto_name = '';
        if (isset($foto['foto']) && $foto['foto']['error'] === UPLOAD_ERR_OK) {
            $foto_tmp = $foto['foto']['tmp_name'];
            $foto_name = uniqid() . '_' . basename($foto['foto']['name']);
            $foto_path = $this->upload_dir . $foto_name;

            // Validasi tipe file
            $allowed = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($foto['foto']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                return [
                    'sukses' => false,
                    'pesan' => 'Format foto tidak valid! Gunakan JPG, JPEG, atau PNG.'
                ];
            }

            // Validasi ukuran file (max 2MB)
            if ($foto['foto']['size'] > 2 * 1024 * 1024) {
                return [
                    'sukses' => false,
                    'pesan' => 'Ukuran foto terlalu besar! Maksimal 2MB.'
                ];
            }

            // Pindahkan file
            if (!move_uploaded_file($foto_tmp, $foto_path)) {
                return [
                    'sukses' => false,
                    'pesan' => 'Gagal mengupload foto! Pastikan folder memiliki permission yang benar.'
                ];
            }
        }

        $result = $this->candidate->tambahKandidat([
            'nama' => $data['nama'],
            'no_urut' => $data['no_urut'],
            'visi' => $data['visi'],
            'misi' => $data['misi'],
            'foto' => $foto_name
        ]);

        if ($result) {
            return [
                'sukses' => true,
                'pesan' => 'Kandidat berhasil ditambahkan!'
            ];
        }

        // Jika gagal, hapus foto yang sudah diupload
        if ($foto_name && file_exists($this->upload_dir . $foto_name)) {
            unlink($this->upload_dir . $foto_name);
        }

        return [
            'sukses' => false,
            'pesan' => 'Gagal menambahkan kandidat!'
        ];
    }

    // Mengupdate data kandidat
    public function updateKandidat($id, $data, $foto = null)
    {
        // Validasi input
        if (empty($data['nama']) || empty($data['visi']) || empty($data['misi']) || empty($data['no_urut'])) {
            return [
                'sukses' => false,
                'pesan' => 'Semua field harus diisi!'
            ];
        }

        // Validasi no_urut unik (kecuali untuk kandidat yang sedang diupdate)
        if ($this->candidate->cekNomorUrut($data['no_urut'], $id)) {
            return [
                'sukses' => false,
                'pesan' => 'Nomor urut sudah digunakan!'
            ];
        }

        // Ambil data kandidat lama
        $kandidat_lama = $this->candidate->ambilKandidatById($id);
        if (!$kandidat_lama) {
            return [
                'sukses' => false,
                'pesan' => 'Kandidat tidak ditemukan!'
            ];
        }

        // Handle upload foto baru jika ada
        $foto_name = $kandidat_lama['foto'];
        if (isset($foto['foto']) && $foto['foto']['error'] === UPLOAD_ERR_OK) {
            $foto_tmp = $foto['foto']['tmp_name'];
            $foto_name = uniqid() . '_' . basename($foto['foto']['name']);
            $foto_path = $this->upload_dir . $foto_name;

            // Validasi tipe file
            $allowed = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($foto['foto']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                return [
                    'sukses' => false,
                    'pesan' => 'Format foto tidak valid! Gunakan JPG, JPEG, atau PNG.'
                ];
            }

            // Validasi ukuran file (max 2MB)
            if ($foto['foto']['size'] > 2 * 1024 * 1024) {
                return [
                    'sukses' => false,
                    'pesan' => 'Ukuran foto terlalu besar! Maksimal 2MB.'
                ];
            }

            // Pindahkan file
            if (!move_uploaded_file($foto_tmp, $foto_path)) {
                return [
                    'sukses' => false,
                    'pesan' => 'Gagal mengupload foto!'
                ];
            }

            // Hapus foto lama jika ada
            if ($kandidat_lama['foto'] && file_exists($this->upload_dir . $kandidat_lama['foto'])) {
                unlink($this->upload_dir . $kandidat_lama['foto']);
            }
        }

        $result = $this->candidate->updateKandidat($id, [
            'nama' => $data['nama'],
            'no_urut' => $data['no_urut'],
            'visi' => $data['visi'],
            'misi' => $data['misi'],
            'foto' => $foto_name
        ]);

        if ($result) {
            return [
                'sukses' => true,
                'pesan' => 'Kandidat berhasil diupdate!'
            ];
        }

        return [
            'sukses' => false,
            'pesan' => 'Gagal mengupdate kandidat!'
        ];
    }

    // Menghapus kandidat
    public function hapusKandidat($id)
    {
        // Cek apakah kandidat sudah memiliki vote
        if ($this->vote->getJumlahVoteKandidat($id) > 0) {
            return [
                'sukses' => false,
                'pesan' => 'Tidak dapat menghapus kandidat yang sudah memiliki vote!'
            ];
        }

        // Ambil data kandidat untuk hapus foto
        $kandidat = $this->candidate->ambilKandidatById($id);
        if ($kandidat && $kandidat['foto']) {
            $foto_path = $this->upload_dir . $kandidat['foto'];
            if (file_exists($foto_path)) {
                unlink($foto_path);
            }
        }

        if ($this->candidate->hapusKandidat($id)) {
            return [
                'sukses' => true,
                'pesan' => 'Kandidat berhasil dihapus!'
            ];
        }

        return [
            'sukses' => false,
            'pesan' => 'Gagal menghapus kandidat!'
        ];
    }

    // Aliases for backward compatibility
    public function getDaftarKandidat()
    {
        return $this->ambilSemuaKandidat();
    }

    public function getKandidatById($id)
    {
        return $this->ambilKandidatById($id);
    }

    // Method untuk handle AJAX request
    public function handleAjaxRequest($action, $data = [], $files = [])
    {
        header('Content-Type: application/json');

        switch ($action) {
            case 'tambah':
                $hasil = $this->tambahKandidat($data, $files);
                echo json_encode($hasil);
                break;

            case 'update':
                if (isset($data['id'])) {
                    $hasil = $this->updateKandidat($data['id'], $data, $files);
                    echo json_encode($hasil);
                }
                break;

            case 'hapus':
                if (isset($data['id'])) {
                    $hasil = $this->hapusKandidat($data['id']);
                    echo json_encode($hasil);
                }
                break;

            case 'get_detail':
                if (isset($data['id'])) {
                    $kandidat = $this->ambilKandidatById($data['id']);
                    echo json_encode([
                        'sukses' => true,
                        'data' => $kandidat
                    ]);
                }
                break;

            default:
                echo json_encode([
                    'sukses' => false,
                    'pesan' => 'Action tidak valid!'
                ]);
        }
        exit;
    }

    // Method untuk handle form submission biasa
    public function handleFormSubmission($action, $data = [], $files = [])
    {
        switch ($action) {
            case 'tambah':
                return $this->tambahKandidat($data, $files);

            case 'update':
                if (isset($data['id'])) {
                    return $this->updateKandidat($data['id'], $data, $files);
                }
                break;

            case 'hapus':
                if (isset($data['id'])) {
                    return $this->hapusKandidat($data['id']);
                }
                break;
        }

        return [
            'sukses' => false,
            'pesan' => 'Action tidak valid!'
        ];
    }
}

// Handle AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $controller = new CandidateController();
    $controller->handleAjaxRequest(
        $_POST['action'] ?? '',
        $_POST,
        $_FILES
    );
}
