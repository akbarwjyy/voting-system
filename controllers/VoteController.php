<?php
// Include constants file first
require_once __DIR__ . '/../config/constants.php';

// Include files dengan path absolut
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/models/Vote.php';
require_once ROOT_PATH . '/models/User.php';
require_once ROOT_PATH . '/models/Candidate.php';

class VoteController
{
    private $db;
    private $vote;
    private $user;
    private $candidate;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getKoneksi();
        $this->vote = new Vote($this->db);
        $this->user = new User($this->db);
        $this->candidate = new Candidate($this->db);
    }

    // Memproses voting dari user
    public function prosesVoting($user_id, $kandidat_id)
    {
        // Cek apakah user sudah memilih
        if ($this->vote->cekSudahMemilih($user_id)) {
            return [
                'sukses' => false,
                'pesan' => 'Anda sudah melakukan voting!'
            ];
        }

        // Cek apakah kandidat valid
        if (!$this->candidate->ambilKandidatById($kandidat_id)) {
            return [
                'sukses' => false,
                'pesan' => 'Kandidat tidak valid!'
            ];
        }

        // Set data vote
        $this->vote->user_id = $user_id;
        $this->vote->candidate_id = $kandidat_id;

        // Proses voting
        if ($this->vote->tambahVote()) {
            return [
                'sukses' => true,
                'pesan' => 'Voting berhasil dilakukan!'
            ];
        }

        return [
            'sukses' => false,
            'pesan' => 'Gagal melakukan voting!'
        ];
    }

    // Mendapatkan hasil voting
    public function getHasilVoting()
    {
        $stmt = $this->vote->hitungSuaraPerKandidat();
        $hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Hitung total suara
        $total_suara = 0;
        foreach ($hasil as $data) {
            $total_suara += intval($data['jumlah_suara']);
        }

        // Tambahkan persentase
        foreach ($hasil as &$data) {
            $data['persentase'] = $total_suara > 0 ?
                round((intval($data['jumlah_suara']) / $total_suara) * 100, 2) : 0;
        }

        return [
            'hasil' => $hasil,
            'total_suara' => $total_suara
        ];
    }

    // Cek status memilih user
    public function cekStatusMemilih($user_id)
    {
        return [
            'sudah_memilih' => $this->vote->cekSudahMemilih($user_id)
        ];
    }

    // Cek status voting (aktif/tidak)
    public function cekStatusVoting()
    {
        try {
            $query = "SELECT status FROM voting_settings WHERE id = 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return [
                    'sukses' => true,
                    'voting_aktif' => (bool)$result['status'],
                    'pesan' => (bool)$result['status'] ? 'Voting sedang berlangsung' : 'Voting belum dimulai atau sudah berakhir'
                ];
            }

            return [
                'sukses' => true,
                'voting_aktif' => false,
                'pesan' => 'Status voting belum diatur'
            ];
        } catch (PDOException $e) {
            return [
                'sukses' => false,
                'voting_aktif' => false,
                'pesan' => 'Terjadi kesalahan saat mengecek status voting'
            ];
        }
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $voteController = new VoteController();
    $hasil = [];

    switch ($_POST['action']) {
        case 'submit_vote':
            if (isset($_POST['user_id'], $_POST['kandidat_id'])) {
                $hasil = $voteController->prosesVoting(
                    $_POST['user_id'],
                    $_POST['kandidat_id']
                );
            }
            break;

        case 'get_hasil':
            $hasil = [
                'sukses' => true,
                'data' => $voteController->getHasilVoting()
            ];
            break;

        case 'cek_status':
            if (isset($_POST['user_id'])) {
                $hasil = [
                    'sukses' => true,
                    'data' => $voteController->cekStatusMemilih($_POST['user_id'])
                ];
            }
            break;

        case 'cek_status_voting':
            $hasil = $voteController->cekStatusVoting();
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
