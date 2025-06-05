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
    }

    // Tambah kandidat baru
    public function tambahKandidat()
    {
        $query = "INSERT INTO " . $this->nama_tabel . " 
                  SET nama = :nama, 
                      visi = :visi, 
                      misi = :misi, 
                      foto = :foto";

        $stmt = $this->koneksi->prepare($query);

        // Bersihkan data
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->visi = htmlspecialchars(strip_tags($this->visi));
        $this->misi = htmlspecialchars(strip_tags($this->misi));
        $this->foto = htmlspecialchars(strip_tags($this->foto));

        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":visi", $this->visi);
        $stmt->bindParam(":misi", $this->misi);
        $stmt->bindParam(":foto", $this->foto);

        return $stmt->execute();
    }

    // Ambil semua kandidat
    public function ambilSemuaKandidat()
    {
        $query = "SELECT id, nama, visi, misi, foto, tanggal_dibuat 
                  FROM " . $this->nama_tabel . " 
                  ORDER BY tanggal_dibuat ASC";

        $stmt = $this->koneksi->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Ambil kandidat berdasarkan ID
    public function ambilKandidatById($kandidat_id)
    {
        $query = "SELECT id, nama, visi, misi, foto, tanggal_dibuat 
                  FROM " . $this->nama_tabel . " 
                  WHERE id = :id LIMIT 1";

        $stmt = $this->koneksi->prepare($query);
        $stmt->bindParam(':id', $kandidat_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update kandidat
    public function updateKandidat()
    {
        $query = "UPDATE " . $this->nama_tabel . " 
                  SET nama = :nama, 
                      visi = :visi, 
                      misi = :misi, 
                      foto = :foto 
                  WHERE id = :id";

        $stmt = $this->koneksi->prepare($query);

        // Bersihkan data
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->visi = htmlspecialchars(strip_tags($this->visi));
        $this->misi = htmlspecialchars(strip_tags($this->misi));
        $this->foto = htmlspecialchars(strip_tags($this->foto));

        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":visi", $this->visi);
        $stmt->bindParam(":misi", $this->misi);
        $stmt->bindParam(":foto", $this->foto);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
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
