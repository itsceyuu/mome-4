<?php
require_once "Model.class.php";

class AkunKlien extends Model {
    // dipakai di login
    // public function cekData($username, $password) {
    //     // Ambil data user berdasarkan username
    //     $stmt = $this->db->prepare("SELECT * FROM mome.users WHERE username=?");
    //     $stmt->bind_param("s", $username);
    //     $stmt->execute();
    //     $result = $stmt->get_result();

    //     // Kalau username ditemukan
    //     if ($row = $result->fetch_assoc()) {
    //         // Cek apakah password cocok (hash verification)
    //         if (password_verify($password, $row['password'])) {
    //             return "valid";
    //         }
    //     }
    //     // Kalau username gak ketemu atau password salah
    //     return "invalid";
    // }
    public function cekData($username, $password) {
        try {
            // Validasi input
            if (empty($username) || empty($password)) {
                return false;
            }

            // Ambil data user berdasarkan username
            // Tidak perlu prefix database karena sudah di-set di Model.class.php
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
            
            if (!$stmt) {
                error_log("Prepare statement failed: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("s", $username);
            
            if (!$stmt->execute()) {
                error_log("Execute failed: " . $stmt->error);
                $stmt->close();
                return false;
            }
            
            $result = $stmt->get_result();

            // Kalau username ditemukan
            if ($row = $result->fetch_assoc()) {
                // Pastikan password ada di database
                if (!isset($row['password']) || empty($row['password'])) {
                    error_log("Password tidak ditemukan untuk user: " . $username);
                    $stmt->close();
                    return false;
                }
                
                // Cek apakah password cocok
                if (password_verify($password, $row['password'])) {
                    // ✅ return seluruh data user (misal: id, username, email, dst.)
                    $stmt->close();
                    return $row;
                } else {
                    error_log("Password tidak cocok untuk user: " . $username);
                }
            } else {
                error_log("Username tidak ditemukan: " . $username);
            }
            
            $stmt->close();
            // Kalau username gak ketemu atau password salah
            return false;
        } catch (Exception $e) {
            error_log("Error in cekData: " . $e->getMessage());
            return false;
        }
    }

    // Cek apakah email sudah digunakan (register)
    public function cekEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Cek apakah username sudah digunakan (register)
    public function cekUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Simpan data akun baru (register)
    public function simpanData($username, $email, $password) {
        // Enkripsi password sebelum disimpan
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'client')");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);
        return $stmt->execute();
    }
}
?>


