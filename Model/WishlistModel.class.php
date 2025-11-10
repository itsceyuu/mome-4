<?php
require_once "Model.class.php";

class WishlistModel extends Model
{

    public function getAllWishlist($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, user_id, item_name, description, date_added 
                FROM wishlist 
                WHERE user_id = ? 
                ORDER BY date_added DESC
            ");

            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return [];
            }

            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $wishlist = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $wishlist;
        } catch (Exception $e) {
            error_log("Error in getAllWishlist: " . $e->getMessage());
            return [];
        }
    }

    public function getWishlistById($id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, user_id, item_name, description, date_added 
                FROM wishlist 
                WHERE id = ?
            ");

            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return null;
            }

            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();
            $stmt->close();

            return $item;
        } catch (Exception $e) {
            error_log("Error in getWishlistById: " . $e->getMessage());
            return null;
        }
    }

    public function createWishlist($userId, $itemName, $description)
    {
        try {
            if (empty($description)) {
                $description = '';
            }

            $stmt = $this->db->prepare("
                INSERT INTO wishlist (user_id, item_name, description, date_added)
                VALUES (?, ?, ?, NOW())
            ");

            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("iss", $userId, $itemName, $description);
            $result = $stmt->execute();

            if (!$result) {
                error_log("Execute failed: " . $stmt->error);
            }

            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error in createWishlist: " . $e->getMessage());
            return false;
        }
    }

    public function updateWishlist($id, $itemName, $description)
    {
        try {
            if (empty($description)) {
                $description = '';
            }

            $stmt = $this->db->prepare("
                UPDATE wishlist 
                SET item_name = ?, description = ?
                WHERE id = ?
            ");

            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("ssi", $itemName, $description, $id);
            $result = $stmt->execute();

            if (!$result) {
                error_log("Execute failed: " . $stmt->error);
            }

            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error in updateWishlist: " . $e->getMessage());
            return false;
        }
    }

    public function deleteWishlist($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM wishlist WHERE id = ?");

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
            error_log("Error in deleteWishlist: " . $e->getMessage());
            return false;
        }
    }

    public function getLatestWishlist($userId, $limit = 1)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, item_name, description, date_added 
                FROM wishlist 
                WHERE user_id = ?
                ORDER BY date_added DESC 
                LIMIT ?
            ");

            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return null;
            }

            $stmt->bind_param("ii", $userId, $limit);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($limit === 1) {
                $item = $result->fetch_assoc();
            } else {
                $item = $result->fetch_all(MYSQLI_ASSOC);
            }

            $stmt->close();

            return $item;
        } catch (Exception $e) {
            error_log("Error in getLatestWishlist: " . $e->getMessage());
            return null;
        }
    }
}
