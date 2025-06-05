<?php
class Admin
{
    private $koneksi;
    private $nama_tabel = "admins";

    public $id;
    public $username;
    public $password;
    public $tanggal_dibuat;

    public function __construct($database_koneksi)
    {
        $this->koneksi = $database_koneksi;
    }

    // Fungsi untuk login admin
    public function masukAdmin()
    {
        $query = "SELECT id, username, password 
                  FROM " . $this->nama_tabel . " 
                  WHERE username = :username LIMIT 1";

        $stmt = $this->koneksi->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();

        $jumlah_row = $stmt->rowCount();

        if ($jumlah_row > 0) {
            $baris = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($this->password, $baris['password'])) {
                $this->id = $baris['id'];
                $this->username = $baris['username'];
                return true;
            }
        }
        return false;
    }

    // Buat admin baru
    public function buatAdmin()
    {
        $query = "INSERT INTO " . $this->nama_tabel . " 
                  SET username = :username, 
                      password = :password";

        $stmt = $this->koneksi->prepare($query);

        // Bersihkan data
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $this->password);

        return $stmt->execute();
    }

    // Cek apakah username sudah ada
    public function cekUsernameSudahAda()
    {
        $query = "SELECT id FROM " . $this->nama_tabel . " WHERE username = :username LIMIT 1";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Ubah password admin
    public function ubahPassword($admin_id, $password_baru)
    {
        $query = "UPDATE " . $this->nama_tabel . " 
                  SET password = :password 
                  WHERE id = :id";

        $stmt = $this->koneksi->prepare($query);
        $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':id', $admin_id);

        return $stmt->execute();
    }
}
