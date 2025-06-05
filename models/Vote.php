<?php
class Vote
{
    private $koneksi;
    private $nama_tabel = "votes";

    public $id;
    public $user_id;
    public $candidate_id;
    public $waktu_vote;

    public function __construct($database_koneksi)
    {
        $this->koneksi = $database_koneksi;
    }

    // Tambah vote baru
    public function tambahVote()
    {
        // Cek apakah user sudah memilih
        if ($this->cekSudahMemilih($this->user_id)) {
            return false;
        }

        // Mulai transaksi
        $this->koneksi->beginTransaction();

        try {
            // Insert vote
            $query = "INSERT INTO " . $this->nama_tabel . " 
                      SET user_id = :user_id, 
                          candidate_id = :candidate_id";

            $stmt = $this->koneksi->prepare($query);
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":candidate_id", $this->candidate_id);
            $stmt->execute();

            // Update status user sudah memilih
            $query_update = "UPDATE users 
                            SET sudah_memilih = 1 
                            WHERE id = :user_id";

            $stmt_update = $this->koneksi->prepare($query_update);
            $stmt_update->bindParam(":user_id", $this->user_id);
            $stmt_update->execute();

            // Commit transaksi
            $this->koneksi->commit();
            return true;
        } catch (Exception $e) {
            // Rollback jika ada error
            $this->koneksi->rollback();
            return false;
        }
    }

    // Cek apakah user sudah memilih
    public function cekSudahMemilih($user_id)
    {
        $query = "SELECT id FROM " . $this->nama_tabel . " 
                  WHERE user_id = :user_id LIMIT 1";

        $stmt = $this->koneksi->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Ambil vote berdasarkan user ID
    public function ambilVoteByUserId($user_id)
    {
        $query = "SELECT v.*, c.nama AS nama_kandidat 
                  FROM " . $this->nama_tabel . " v 
                  JOIN candidates c ON v.candidate_id = c.id 
                  WHERE v.user_id = :user_id LIMIT 1";

        $stmt = $this->koneksi->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Hitung total suara per kandidat
    public function hitungSuaraPerKandidat()
    {
        $query = "SELECT c.id, c.nama, c.foto, COUNT(v.id) as jumlah_suara
                  FROM candidates c 
                  LEFT JOIN " . $this->nama_tabel . " v ON c.id = v.candidate_id 
                  GROUP BY c.id 
                  ORDER BY jumlah_suara DESC, c.nama ASC";

        $stmt = $this->koneksi->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Hitung total semua suara
    public function hitungTotalSuara()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->nama_tabel;
        $stmt = $this->koneksi->prepare($query);
        $stmt->execute();

        $baris = $stmt->fetch(PDO::FETCH_ASSOC);
        return $baris['total'];
    }

    // Ambil semua vote dengan detail
    public function ambilSemuaVoteDetail()
    {
        $query = "SELECT v.id, v.waktu_vote, 
                         u.nama AS nama_pemilih, u.email,
                         c.nama AS nama_kandidat
                  FROM " . $this->nama_tabel . " v 
                  JOIN users u ON v.user_id = u.id 
                  JOIN candidates c ON v.candidate_id = c.id 
                  ORDER BY v.waktu_vote DESC";

        $stmt = $this->koneksi->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Hapus vote (untuk admin jika diperlukan)
    public function hapusVote($vote_id)
    {
        // Mulai transaksi
        $this->koneksi->beginTransaction();

        try {
            // Ambil data vote terlebih dahulu
            $query_get = "SELECT user_id FROM " . $this->nama_tabel . " WHERE id = :id";
            $stmt_get = $this->koneksi->prepare($query_get);
            $stmt_get->bindParam(':id', $vote_id);
            $stmt_get->execute();
            $vote_data = $stmt_get->fetch(PDO::FETCH_ASSOC);

            if ($vote_data) {
                // Hapus vote
                $query_delete = "DELETE FROM " . $this->nama_tabel . " WHERE id = :id";
                $stmt_delete = $this->koneksi->prepare($query_delete);
                $stmt_delete->bindParam(':id', $vote_id);
                $stmt_delete->execute();

                // Update status user belum memilih
                $query_update = "UPDATE users 
                                SET sudah_memilih = 0 
                                WHERE id = :user_id";

                $stmt_update = $this->koneksi->prepare($query_update);
                $stmt_update->bindParam(":user_id", $vote_data['user_id']);
                $stmt_update->execute();

                // Commit transaksi
                $this->koneksi->commit();
                return true;
            }

            $this->koneksi->rollback();
            return false;
        } catch (Exception $e) {
            // Rollback jika ada error
            $this->koneksi->rollback();
            return false;
        }
    }

    // Ambil statistik voting
    public function ambilStatistikVoting()
    {
        $query = "SELECT 
                    (SELECT COUNT(*) FROM users WHERE is_active = 1) as total_pemilih_aktif,
                    (SELECT COUNT(*) FROM users WHERE sudah_memilih = 1) as total_sudah_memilih,
                    (SELECT COUNT(*) FROM candidates) as total_kandidat,
                    (SELECT COUNT(*) FROM " . $this->nama_tabel . ") as total_suara";

        $stmt = $this->koneksi->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
