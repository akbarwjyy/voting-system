<?php
require_once '../config/database.php';
require_once '../models/Candidate.php';
require_once '../models/Vote.php';

class CandidateController
{
    private $db;
    private $candidate;
    private $vote;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getKoneksi();
        $this->candidate = new Candidate($this->db);
        $this->vote = new Vote($this->db);
    }    // Mendapatkan semua kandidat
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
        if (empty($data['nama']) || empty($data['visi']) || empty($data['misi'])) {
            return [
                'sukses' => false,
                'pesan' => 'Semua field harus diisi!'
            ];
        }

        // Handle upload foto
        $foto_name = '';
        if (isset($foto['foto']) && $foto['foto']['error'] === UPLOAD_ERR_OK) {
            $foto_tmp = $foto['foto']['tmp_name'];
            $foto_name = uniqid() . '_' . basename($foto['foto']['name']);
            $foto_path = '../assets/uploads/kandidat/' . $foto_name;

            // Validasi tipe file
            $allowed = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($foto['foto']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                return [
                    'sukses' => false,
                    'pesan' => 'Format foto tidak valid! Gunakan JPG, JPEG, atau PNG.'
                ];
            }

            // Pindahkan file
            if (!move_uploaded_file($foto_tmp, $foto_path)) {
                return [
                    'sukses' => false,
                    'pesan' => 'Gagal mengupload foto!'
                ];
            }
        }

        // Simpan data kandidat
        $kandidat = [
            'nama' => $data['nama'],
            'foto' => $foto_name,
            'visi' => $data['visi'],
            'misi' => $data['misi'],
            'no_urut' => $data['no_urut']
        ];

        if ($this->candidate->tambahKandidat($kandidat)) {
            return [
                'sukses' => true,
                'pesan' => 'Kandidat berhasil ditambahkan!'
            ];
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
        if (empty($data['nama']) || empty($data['visi']) || empty($data['misi'])) {
            return [
                'sukses' => false,
                'pesan' => 'Semua field harus diisi!'
            ];
        }        // Ambil data kandidat lama
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
            $foto_path = '../assets/uploads/kandidat/' . $foto_name;

            // Validasi tipe file
            $allowed = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($foto['foto']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                return [
                    'sukses' => false,
                    'pesan' => 'Format foto tidak valid! Gunakan JPG, JPEG, atau PNG.'
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
            if ($kandidat_lama['foto'] && file_exists('../assets/uploads/kandidat/' . $kandidat_lama['foto'])) {
                unlink('../assets/uploads/kandidat/' . $kandidat_lama['foto']);
            }
        }

        // Update data kandidat
        $kandidat = [
            'id' => $id,
            'nama' => $data['nama'],
            'foto' => $foto_name,
            'visi' => $data['visi'],
            'misi' => $data['misi'],
            'no_urut' => $data['no_urut']
        ];

        if ($this->candidate->updateKandidat($kandidat)) {
            return [
                'sukses' => true,
                'pesan' => 'Data kandidat berhasil diperbarui!'
            ];
        }

        return [
            'sukses' => false,
            'pesan' => 'Gagal memperbarui data kandidat!'
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
        }        // Ambil data kandidat untuk hapus foto
        $kandidat = $this->candidate->ambilKandidatById($id);
        if ($kandidat && $kandidat['foto']) {
            $foto_path = '../assets/uploads/kandidat/' . $kandidat['foto'];
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
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $kandidatController = new CandidateController();
    $hasil = [];

    switch ($_POST['action']) {
        case 'tambah':
            $hasil = $kandidatController->tambahKandidat($_POST, $_FILES);
            break;

        case 'update':
            if (isset($_POST['id'])) {
                $hasil = $kandidatController->updateKandidat($_POST['id'], $_POST, $_FILES);
            }
            break;

        case 'hapus':
            if (isset($_POST['id'])) {
                $hasil = $kandidatController->hapusKandidat($_POST['id']);
            }
            break;
        case 'get_all':
            $hasil = [
                'sukses' => true,
                'data' => $kandidatController->ambilSemuaKandidat()
            ];
            break;

        case 'get_detail':
            if (isset($_POST['id'])) {
                $hasil = [
                    'sukses' => true,
                    'data' => $kandidatController->ambilKandidatById($_POST['id'])
                ];
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
