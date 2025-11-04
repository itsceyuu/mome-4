<?php
require_once "Model.class.php";

class AkunKlien extends Model {
    // dipakai di login
    public function cekData($username, $password) {
        // Ambil data user berdasarkan username
        $stmt = $this->db->prepare("SELECT * FROM mome.users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Kalau username ditemukan
        if ($row = $result->fetch_assoc()) {
            // Cek apakah password cocok (hash verification)
            if (password_verify($password, $row['password'])) {
                return "valid";
            }
        }
        // Kalau username gak ketemu atau password salah
        return "invalid";
    }

    // Cek apakah email sudah digunakan (register)
    public function cekEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM mome.users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Cek apakah username sudah digunakan (register)
    public function cekUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM mome.users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Simpan data akun baru (register)
    public function simpanData($username, $email, $password) {
    // Enkripsi password sebelum disimpan
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("INSERT INTO mome.users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss",  $username, $email, $hashedPassword);
        return $stmt->execute();
    }
}
?>
