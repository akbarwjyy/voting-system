<?php
class Candidate
{
    private $koneksi;
    private $nama_tabel = "candidates";

    public $id;
    public $nama;
    public $visi;
    public $misi;
    public $foto;
    public $tanggal_dibuat;

    public function __construct($database_koneksi)
    {
        $this->koneksi = $database_koneksi;
    }    // Cek apakah nomor urut sudah digunakan
    public function cekNomorUrut($no_urut, $id = null)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->nama_tabel . " WHERE no_urut = :no_urut";
        if ($id !== null) {
            $query .= " AND id != :id";
        }

        $stmt = $this->koneksi->prepare($query);
        $stmt->bindParam(":no_urut", $no_urut);
        if ($id !== null) {
            $stmt->bindParam(":id", $id);
        }
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    // Tambah kandidat baru
    public function tambahKandidat($data)
    {
        try {
            // Cek apakah nomor urut sudah digunakan
            if ($this->cekNomorUrut($data['no_urut'])) {
                throw new Exception("Nomor urut " . $data['no_urut'] . " sudah digunakan!");
            }

            $query = "INSERT INTO " . $this->nama_tabel . " 
                    SET nama = :nama, 
                        visi = :visi, 
                        misi = :misi, 
                        foto = :foto,
                        no_urut = :no_urut";

            $stmt = $this->koneksi->prepare($query);

            // Bersihkan data
            $nama = htmlspecialchars(strip_tags($data['nama']));
            $visi = htmlspecialchars(strip_tags($data['visi']));
            $misi = htmlspecialchars(strip_tags($data['misi']));
            $foto = htmlspecialchars(strip_tags($data['foto']));
            $no_urut = htmlspecialchars(strip_tags($data['no_urut']));

            $stmt->bindParam(":nama", $nama);
            $stmt->bindParam(":visi", $visi);
            $stmt->bindParam(":misi", $misi);
            $stmt->bindParam(":foto", $foto);
            $stmt->bindParam(":no_urut", $no_urut);

            $stmt->execute();
            return ['sukses' => true, 'pesan' => 'Kandidat berhasil ditambahkan!'];
        } catch (Exception $e) {
            return ['sukses' => false, 'pesan' => $e->getMessage()];
        }
    }    // Ambil semua kandidat
    public function ambilSemuaKandidat()
    {
        $query = "SELECT id, nama, visi, misi, foto, no_urut, tanggal_dibuat 
                  FROM " . $this->nama_tabel . " 
                  ORDER BY no_urut ASC";

        $stmt = $this->koneksi->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Ambil kandidat berdasarkan ID
    public function ambilKandidatById($kandidat_id)
    {
        $query = "SELECT id, nama, visi, misi, foto, no_urut, tanggal_dibuat 
                  FROM " . $this->nama_tabel . " 
                  WHERE id = :id LIMIT 1";

        $stmt = $this->koneksi->prepare($query);
        $stmt->bindParam(':id', $kandidat_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update kandidat    
    public function updateKandidat($data)
    {
        try {
            // Cek apakah nomor urut sudah digunakan oleh kandidat lain
            if ($this->cekNomorUrut($data['no_urut'], $data['id'])) {
                throw new Exception("Nomor urut " . $data['no_urut'] . " sudah digunakan!");
            }

            $query = "UPDATE " . $this->nama_tabel . " 
                    SET nama = :nama, 
                        visi = :visi, 
                        misi = :misi, 
                        foto = :foto,
                        no_urut = :no_urut 
                    WHERE id = :id";

            $stmt = $this->koneksi->prepare($query);

            // Bersihkan data
            $nama = htmlspecialchars(strip_tags($data['nama']));
            $visi = htmlspecialchars(strip_tags($data['visi']));
            $misi = htmlspecialchars(strip_tags($data['misi']));
            $foto = htmlspecialchars(strip_tags($data['foto']));
            $no_urut = htmlspecialchars(strip_tags($data['no_urut']));
            $id = htmlspecialchars(strip_tags($data['id']));

            $stmt->bindParam(":nama", $nama);
            $stmt->bindParam(":visi", $visi);
            $stmt->bindParam(":misi", $misi);
            $stmt->bindParam(":foto", $foto);
            $stmt->bindParam(":no_urut", $no_urut);
            $stmt->bindParam(":id", $id);

            $stmt->execute();
            return ['sukses' => true, 'pesan' => 'Data kandidat berhasil diperbarui!'];
        } catch (Exception $e) {
            return ['sukses' => false, 'pesan' => $e->getMessage()];
        }
    }

    // Hapus kandidat
    public function hapusKandidat($kandidat_id)
    {
        $query = "DELETE FROM " . $this->nama_tabel . " WHERE id = :id";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bindParam(':id', $kandidat_id);

        return $stmt->execute();
    }

    // Hitung total kandidat
    public function hitungTotalKandidat()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->nama_tabel;
        $stmt = $this->koneksi->prepare($query);
        $stmt->execute();

        $baris = $stmt->fetch(PDO::FETCH_ASSOC);
        return $baris['total'];
    }

    // Ambil kandidat dengan jumlah suara
    public function ambilKandidatDenganSuara()
    {
        $query = "SELECT c.id, c.nama, c.visi, c.misi, c.foto, 
                         COUNT(v.id) as jumlah_suara
                  FROM " . $this->nama_tabel . " c 
                  LEFT JOIN votes v ON c.id = v.candidate_id 
                  GROUP BY c.id 
                  ORDER BY jumlah_suara DESC, c.nama ASC";

        $stmt = $this->koneksi->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}
