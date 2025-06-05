<?php
class User
{
    private $koneksi;
    private $nama_tabel = "users";
    public $id;
    public $nama;
    public $email;
    public $password;
    public $is_active;
    public $sudah_memilih;
    public $tanggal_daftar;

    public function __construct($database_koneksi)
    {
        $this->koneksi = $database_koneksi;
    }

    // Fungsi untuk mendaftar user baru
    public function daftarUser()
    {
        // Cek apakah email sudah terdaftar
        if ($this->cekEmailSudahAda()) {
            return false;
        }

        $query = "INSERT INTO " . $this->nama_tabel . " 
                  SET nama = :nama, 
                      email = :email, 
                      password = :password, 
                      is_active = 0, 
                      sudah_memilih = 0";

        $stmt = $this->koneksi->prepare($query);

        // Bersihkan data
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        // Bind parameter
        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Fungsi untuk login user
    public function masukUser()
    {
        $query = "SELECT id, nama, email, password, is_active, sudah_memilih 
                  FROM " . $this->nama_tabel . " 
                  WHERE email = :email AND is_active = 1 LIMIT 1";

        $stmt = $this->koneksi->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        $jumlah_row = $stmt->rowCount();

        if ($jumlah_row > 0) {
            $baris = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($this->password, $baris['password'])) {
                $this->id = $baris['id'];
                $this->nama = $baris['nama'];
                $this->is_active = $baris['is_active'];
                $this->sudah_memilih = $baris['sudah_memilih'];
                return true;
            }
        }
        return false;
    }

    // Cek apakah email sudah terdaftar
    public function cekEmailSudahAda()
    {
        $query = "SELECT id FROM " . $this->nama_tabel . " WHERE email = :email LIMIT 1";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Ambil semua data user untuk admin
    public function ambilSemuaUser()
    {
        $query = "SELECT id, nama, email, is_active, sudah_memilih, tanggal_daftar 
                  FROM " . $this->nama_tabel . " 
                  ORDER BY tanggal_daftar DESC";

        $stmt = $this->koneksi->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Aktifkan atau nonaktifkan user
    public function ubahStatusAktif($user_id, $status)
    {
        $query = "UPDATE " . $this->nama_tabel . " 
                  SET is_active = :status 
                  WHERE id = :id";

        $stmt = $this->koneksi->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $user_id);

        return $stmt->execute();
    }

    // Tandai user sudah memilih
    public function tandaiSudahMemilih($user_id)
    {
        $query = "UPDATE " . $this->nama_tabel . " 
                  SET sudah_memilih = 1 
                  WHERE id = :id";

        $stmt = $this->koneksi->prepare($query);
        $stmt->bindParam(':id', $user_id);

        return $stmt->execute();
    }

    // Cek status sudah memilih
    public function cekSudahMemilih($user_id)
    {
        $query = "SELECT sudah_memilih FROM " . $this->nama_tabel . " WHERE id = :id";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();

        $baris = $stmt->fetch(PDO::FETCH_ASSOC);
        return $baris ? $baris['sudah_memilih'] : false;
    }

    // Ambil data user berdasarkan ID
    public function ambilUserById($user_id)
    {
        $query = "SELECT id, nama, email, is_active, sudah_memilih 
                  FROM " . $this->nama_tabel . " 
                  WHERE id = :id LIMIT 1";

        $stmt = $this->koneksi->prepare($query);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Hapus user
    public function hapusUser($user_id)
    {
        $query = "DELETE FROM " . $this->nama_tabel . " WHERE id = :id";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bindParam(':id', $user_id);

        return $stmt->execute();
    }
}
