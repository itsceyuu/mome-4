<?php
require_once "Model.class.php";

class AkunKlienModel extends Model {

    public function cekData($username, $password) {
        // Siapkan query
        $stmt = $this->db->prepare("SELECT * FROM akun WHERE username=? AND password=?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return "valid";
        } else {
            return "invalid";
        }
    }
}
