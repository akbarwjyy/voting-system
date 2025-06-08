<?php
// Include constants file first
require_once __DIR__ . '/../config/constants.php';

// Include files dengan path absolut
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/models/Vote.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Candidate.php';
require_once BASE_PATH . '/models/VotingSetting.php';

class VoteController
{
    private $db;
    private $vote;
    private $user;
    private $candidate;
    private $votingSetting;
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getKoneksi();
        $this->vote = new Vote($this->db);
        $this->user = new User($this->db);
        $this->candidate = new Candidate($this->db);
        $this->votingSetting = new VotingSetting($this->db);
    }    // Memproses voting dari user
    public function prosesVoting($user_id, $kandidat_id)
    {
        // Cek apakah voting sedang aktif
        $statusVoting = $this->cekStatusVoting();
        if (!$statusVoting['voting_aktif']) {
            return [
                'sukses' => false,
                'pesan' => $statusVoting['pesan']
            ];
        }

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
    }    // Mendapatkan hasil voting
    public function getHasilVoting()
    {
        try {
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
        } catch (Exception $e) {
            return [
                'hasil' => [],
                'total_suara' => 0,
                'error' => $e->getMessage()
            ];
        }

        return [
            'hasil' => $hasil,
            'total_suara' => $total_suara
        ];
    }    // Cek status memilih user
    public function cekStatusMemilih($user_id)
    {
        return [
            'sudah_memilih' => $this->vote->cekSudahMemilih($user_id)
        ];
    }    // Cek status voting
    public function cekStatusVoting()
    {
        $status = $this->votingSetting->getStatus();

        if (!$status['sukses']) {
            return [
                'sukses' => false,
                'voting_aktif' => false,
                'dalam_periode_waktu' => false,
                'pesan' => 'Pengaturan voting tidak tersedia'
            ];
        }

        // Get the actual status values
        return [
            'sukses' => true,
            'voting_aktif' => (bool)$status['voting_aktif'],
            'dalam_periode_waktu' => (bool)$status['dalam_periode_waktu'],
            'waktu_mulai' => $status['waktu_mulai'],
            'waktu_selesai' => $status['waktu_selesai'],
            'pesan' => $status['voting_aktif'] ? 'Voting sedang berlangsung' : 'Voting tidak aktif'
        ];
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
