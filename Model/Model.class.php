<?php

class Model {
    protected $db;

    public function __construct() {
        // 
        $host = 'localhost';
        $port = 3306;
        $user = 'root';
        $pass = '';
        $dbname = 'MOME'; 

        //pake SQL
        $this->db = new mysqli($host, $user, $pass, $dbname, $port);

        //Cek koneksi
        if ($this->db->connect_error) {
            die("Koneksi database gagal: " . $this->db->connect_error);
        }
    }
}
