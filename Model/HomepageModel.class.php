<?php
require_once "Model.class.php";

class HomepageModel extends Model {

    public function getHomepageData($idKlien) {
        // Initialize default values
        $recap = ['total_income' => 0, 'total_expense' => 0];
        $goal = null;
        $wishlist = null;
        $article = null;

        // Data recap - gunakan prepared statement untuk keamanan (OOP)
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COALESCE(SUM(CASE WHEN type='income' THEN amount ELSE 0 END), 0) AS total_income,
                    COALESCE(SUM(CASE WHEN type='expense' THEN amount ELSE 0 END), 0) AS total_expense
                FROM transactions
                WHERE user_id = ?
                    AND MONTH(date) = MONTH(CURRENT_DATE())
                    AND YEAR(date) = YEAR(CURRENT_DATE())
            ");
            if ($stmt) {
                $stmt->bind_param("i", $idKlien);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result) {
                    $recap = $result->fetch_assoc() ?: $recap;
                }
                $stmt->close();
            }
        } catch (Exception $e) {
            error_log("Error getting recap data: " . $e->getMessage());
        }

        // Goals - menggunakan prepared statement (OOP)
        try {
            $stmt = $this->db->prepare("
                SELECT target_name, target_amount, current_amount
                FROM saving_targets
                WHERE user_id = ?
                ORDER BY id DESC LIMIT 1
            ");
            if ($stmt) {
                $stmt->bind_param("i", $idKlien);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result) {
                    $goal = $result->fetch_assoc();
                }
                $stmt->close();
            }
        } catch (Exception $e) {
            error_log("Error getting goal data: " . $e->getMessage());
        }

        // Wishlist - menggunakan prepared statement (OOP)
        try {
            $stmt = $this->db->prepare("
                SELECT item_name, target_amount, description
                FROM wishlist
                WHERE user_id = ?
                ORDER BY date_added DESC LIMIT 1
            ");
            if ($stmt) {
                $stmt->bind_param("i", $idKlien);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result) {
                    $wishlist = $result->fetch_assoc();
                }
                $stmt->close();
            }
        } catch (Exception $e) {
            error_log("Error getting wishlist data: " . $e->getMessage());
        }

        // Artikel - menggunakan prepared statement (OOP)
        try {
            $stmt = $this->db->prepare("
                SELECT title, infoTambahan, published_date
                FROM articles
                ORDER BY published_date DESC LIMIT 1
            ");
            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result) {
                    $article = $result->fetch_assoc();
                }
                $stmt->close();
            }
        } catch (Exception $e) {
            // Jika table articles tidak ada atau error, return null
            error_log("Error getting article data: " . $e->getMessage());
            $article = null;
        }

        return [
            'recap' => $recap ? $recap : ['total_income' => 0, 'total_expense' => 0],
            'goal' => $goal ? $goal : null,
            'wishlist' => $wishlist ? $wishlist : null,
            'article' => $article ? $article : null
        ];
    }
}
?>