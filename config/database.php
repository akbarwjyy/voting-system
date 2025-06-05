<?php
class Database
{
    private $host = 'localhost';
    private $nama_database = 'db_voting';
    private $username = 'root';
    private $password = '';
    private $koneksi;

    public function getKoneksi()
    {
        $this->koneksi = null;

        try {
            $this->koneksi = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->nama_database . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Koneksi database gagal: " . $exception->getMessage();
            die();
        }

        return $this->koneksi;
    }

    public function tutupKoneksi()
    {
        $this->koneksi = null;
    }
}
