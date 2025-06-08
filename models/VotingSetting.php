<?php
class VotingSetting
{
    private $koneksi;
    private $nama_tabel = "pengaturan_voting";

    public function __construct($database_koneksi)
    {
        $this->koneksi = $database_koneksi;
    }

    public function getStatus()
    {
        try {
            $query = "SELECT * FROM " . $this->nama_tabel . " WHERE id = 1";
            $stmt = $this->koneksi->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                // Set timezone to Asia/Jakarta
                date_default_timezone_set('Asia/Jakarta');
                $now = new DateTime();

                // Convert database timestamps to DateTime objects with proper timezone
                $start = null;
                $end = null;

                if (!empty($result['waktu_mulai'])) {
                    $start = DateTime::createFromFormat('Y-m-d H:i:s', $result['waktu_mulai']);
                    if (!$start) {
                        $start = DateTime::createFromFormat('Y-m-d\TH:i', $result['waktu_mulai']);
                    }
                }

                if (!empty($result['waktu_selesai'])) {
                    $end = DateTime::createFromFormat('Y-m-d H:i:s', $result['waktu_selesai']);
                    if (!$end) {
                        $end = DateTime::createFromFormat('Y-m-d\TH:i', $result['waktu_selesai']);
                    }
                }

                // Determine if voting should be active based on time constraints
                $isTimeValid = true;
                if ($start && $end) {
                    $isTimeValid = $now >= $start && $now <= $end;
                } elseif ($start) {
                    $isTimeValid = $now >= $start;
                } elseif ($end) {
                    $isTimeValid = $now <= $end;
                }                // Voting is active if current time is within the scheduled period
                $isActive = $isTimeValid;                // Update voting_aktif based on time validity
                $query_update = "UPDATE " . $this->nama_tabel . " SET voting_aktif = :is_active WHERE id = 1";
                $stmt_update = $this->koneksi->prepare($query_update);
                $stmt_update->bindValue(':is_active', $isActive ? 1 : 0, PDO::PARAM_INT);
                $stmt_update->execute();
                return [
                    'sukses' => true,
                    'voting_aktif' => (bool)$result['voting_aktif'],
                    'waktu_mulai' => $result['waktu_mulai'],
                    'waktu_selesai' => $result['waktu_selesai'],
                    'judul_voting' => $result['judul_voting'],
                    'dalam_periode_waktu' => $isTimeValid
                ];
            }
            return [
                'sukses' => false,
                'pesan' => 'Data pengaturan voting tidak ditemukan'
            ];
        } catch (PDOException $e) {
            return [
                'sukses' => false,
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
    public function updateStatus($data)
    {
        try {
            if (empty($data['waktu_mulai']) || empty($data['waktu_selesai'])) {
                return [
                    'sukses' => false,
                    'pesan' => 'Waktu mulai dan waktu selesai harus diisi'
                ];
            }

            // Validasi format waktu
            $waktuMulai = DateTime::createFromFormat('Y-m-d\TH:i', $data['waktu_mulai']);
            $waktuSelesai = DateTime::createFromFormat('Y-m-d\TH:i', $data['waktu_selesai']);

            if (!$waktuMulai || !$waktuSelesai) {
                return [
                    'sukses' => false,
                    'pesan' => 'Format waktu tidak valid'
                ];
            }

            // Set timezone
            date_default_timezone_set('Asia/Jakarta');
            $now = new DateTime();

            // Konversi ke format MySQL datetime
            $waktuMulaiStr = $waktuMulai->format('Y-m-d H:i:s');
            $waktuSelesaiStr = $waktuSelesai->format('Y-m-d H:i:s');

            // Cek apakah waktu sekarang dalam periode voting
            $isActive = ($now >= $waktuMulai && $now <= $waktuSelesai) ? 1 : 0;

            $query = "UPDATE " . $this->nama_tabel . " 
                    SET waktu_mulai = :waktu_mulai,
                        waktu_selesai = :waktu_selesai,
                        judul_voting = :judul_voting,
                        voting_aktif = :voting_aktif
                    WHERE id = 1";
            $stmt = $this->koneksi->prepare($query);
            $stmt->bindParam(':waktu_mulai', $waktuMulaiStr);
            $stmt->bindParam(':waktu_selesai', $waktuSelesaiStr);
            $stmt->bindParam(':judul_voting', $data['judul_voting']);
            $stmt->bindParam(':voting_aktif', $isActive, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Cek status setelah update
                $isTimeValid = $now >= $waktuMulai && $now <= $waktuSelesai;

                return [
                    'sukses' => true,
                    'pesan' => $isTimeValid ?
                        'Pengaturan berhasil disimpan. Voting sudah aktif.' :
                        'Pengaturan berhasil disimpan. Voting akan aktif pada waktu yang ditentukan.'
                ];
            }

            return [
                'sukses' => false,
                'pesan' => 'Gagal memperbarui pengaturan voting'
            ];
        } catch (PDOException $e) {
            return [
                'sukses' => false,
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
}
