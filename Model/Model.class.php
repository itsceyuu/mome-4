<?php

class Model {
    protected $db;

    public function __construct() {
        // 
        $host = 'localhost';
        $port = 3306;
        $user = 'root';
        $pass = '';
        $dbname = 'mome'; // ganti dengan nama database kamu

        //pake SQL
        $this->db = new mysqli($host, $port, $user, $pass, $dbname);

        //Cek koneksi
        if ($this->db->connect_error) {
            die("Koneksi database gagal: " . $this->db->connect_error);
        }
    }
}
