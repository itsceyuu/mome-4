<?php
require_once "Model.class.php";

class ExpenseModel extends Model {

    // 🔹 Ambil semua transaksi milik 1 klien
    public function getAllTransactions($id) {
        $stmt = $this->db->prepare("
            SELECT id, title, amount, type, date, description 
            FROM transactions 
            WHERE user_id = ? 
            ORDER BY date DESC
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 🔹 Ambil transaksi berdasarkan type (income/expense) - OOP pattern
    public function getTransactionsByType($userId, $type) {
        $stmt = $this->db->prepare("
            SELECT id, title, amount, type, date, description 
            FROM transactions 
            WHERE user_id = ? AND type = ?
            ORDER BY date DESC
        ");
        $stmt->bind_param("is", $userId, $type);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 🔹 Tambah transaksi baru - OOP pattern dengan error handling
    public function addTransaction($userId, $title, $date, $amount, $type, $description) {
        try {
            // Pastikan description tidak null
            if (empty($description)) {
                $description = '';
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO transactions (user_id, title, date, amount, type, description)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("issdss", $userId, $title, $date, $amount, $type, $description);
            $result = $stmt->execute();
            
            if (!$result) {
                error_log("Execute failed: " . $stmt->error);
            }
            
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error in addTransaction: " . $e->getMessage());
            return false;
        }
    }

    // 🔹 Update transaksi - OOP pattern dengan error handling
    public function updateTransaction($transactionId, $title, $date, $amount, $type, $description) {
        try {
            // Pastikan description tidak null
            if (empty($description)) {
                $description = '';
            }
            
            $stmt = $this->db->prepare("
                UPDATE transactions 
                SET title = ?, date = ?, amount = ?, type = ?, description = ? 
                WHERE id = ?
            ");
            
            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("ssdssi", $title, $date, $amount, $type, $description, $transactionId);
            $result = $stmt->execute();
            
            if (!$result) {
                error_log("Execute failed: " . $stmt->error);
            }
            
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error in updateTransaction: " . $e->getMessage());
            return false;
        }
    }

    // 🔹 Hapus transaksi berdasarkan ID - OOP pattern dengan error handling
    public function deleteTransaction($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM transactions WHERE id = ?");
            
            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            
            if (!$result) {
                error_log("Execute failed: " . $stmt->error);
            }
            
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error in deleteTransaction: " . $e->getMessage());
            return false;
        }
    }

    // 🔹 Ambil total income & expense (misalnya untuk ringkasan dashboard)
    public function getTodaySummary($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS total_income_today,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS total_expense_today
            FROM transactions
            WHERE user_id = ? AND date = CURDATE()
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getTransactionById($transactionId) {
        $stmt = $this->db->prepare("
            SELECT id, title, amount, type, date, description 
            FROM transactions 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $transactionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // 🔹 Ambil total income & expense keseluruhan user (untuk halaman Recap)
    public function getTotalSummary($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS total_income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS total_expense
            FROM transactions
            WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // 🔹 Ambil total income & expense per bulan (buat recap bulanan)
    public function getMonthlySummary($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(date, '%Y-%m') AS month,
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS total_income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS total_expense
            FROM transactions
            WHERE user_id = ?
            GROUP BY DATE_FORMAT(date, '%Y-%m')
            ORDER BY month ASC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

}


