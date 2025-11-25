<?php
require_once "Model.class.php";

class GoalsModel extends Model
{

    // CREATE - tambah goal baru
    public function addGoal($user_id, $title, $target_amount, $current_amount, $description, $deadline)
    {
        $stmt = $this->db->prepare("
            INSERT INTO saving_targets (user_id, target_name, target_amount, current_amount, description, deadline)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            die("Prepare failed: " . $this->db->error);
        }

        $stmt->bind_param("isddss", $user_id, $title, $target_amount, $current_amount, $description, $deadline);

        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }

        return $stmt->insert_id;
    }

    // READ - semua goals milik user tertentu
    public function getGoalsByUser($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM mome.saving_targets WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // READ - satu goal berdasarkan id (dengan validasi user)
    public function getGoalById($id, $user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM saving_targets WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // UPDATE - edit goal lengkap
    public function updateGoal($id, $user_id, $title, $target_amount, $current_amount, $description, $deadline)
    {
        $stmt = $this->db->prepare("
            UPDATE saving_targets
            SET target_name = ?, target_amount = ?, current_amount = ?, description = ?, deadline = ?
            WHERE id = ? AND user_id = ?
        ");

        if (!$stmt) {
            die("Prepare failed: " . $this->db->error);
        }

        $stmt->bind_param("sddssii", $title, $target_amount, $current_amount, $description, $deadline, $id, $user_id);

        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }

        return $stmt->affected_rows;
    }

    // UPDATE PROGRESS - update current_amount saja (untuk nabung bertahap)
    public function updateProgress($id, $user_id, $new_amount)
    {
        $stmt = $this->db->prepare("
            UPDATE saving_targets
            SET current_amount = ?
            WHERE id = ? AND user_id = ?
        ");

        if (!$stmt) {
            die("Prepare failed: " . $this->db->error);
        }

        $stmt->bind_param("dii", $new_amount, $id, $user_id);

        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }

        return $stmt->affected_rows;
    }

    // DELETE - hapus goal
    public function deleteGoal($id, $user_id)
    {
        $stmt = $this->db->prepare("DELETE FROM saving_targets WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        return $stmt->execute();
    }

    // HELPER - hitung total progress semua goals user
    public function getTotalProgress($user_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(target_amount) as total_target,
                SUM(current_amount) as total_current
            FROM saving_targets 
            WHERE user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
