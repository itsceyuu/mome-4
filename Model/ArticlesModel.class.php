<?php
require_once "Model.class.php";

class ArticlesModel extends Model
{

    public function getAllArticles()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, title, infoTambahan, content, photo_path, published_date 
                FROM articles 
                ORDER BY published_date DESC
            ");

            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return [];
            }

            $stmt->execute();
            $result = $stmt->get_result();
            $articles = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $articles;
        } catch (Exception $e) {
            error_log("Error in getAllArticles: " . $e->getMessage());
            return [];
        }
    }

    public function getArticleById($id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, title, infoTambahan, content, photo_path, published_date 
                FROM articles 
                WHERE id = ?
            ");

            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return null;
            }

            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $article = $result->fetch_assoc();
            $stmt->close();

            return $article;
        } catch (Exception $e) {
            error_log("Error in getArticleById: " . $e->getMessage());
            return null;
        }
    }

    public function searchArticles($keyword)
    {
        try {
            $searchTerm = "%" . $keyword . "%";

            $stmt = $this->db->prepare("
                SELECT id, title, infoTambahan, content, photo_path, published_date 
                FROM articles 
                WHERE title LIKE ? OR infoTambahan LIKE ? OR content LIKE ?
                ORDER BY published_date DESC
            ");

            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return [];
            }

            $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
            $articles = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $articles;
        } catch (Exception $e) {
            error_log("Error in searchArticles: " . $e->getMessage());
            return [];
        }
    }

    public function getLatestArticles($limit = 5)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, title, infoTambahan, photo_path, published_date 
                FROM articles 
                ORDER BY published_date DESC 
                LIMIT ?
            ");

            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return [];
            }

            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            $articles = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $articles;
        } catch (Exception $e) {
            error_log("Error in getLatestArticles: " . $e->getMessage());
            return [];
        }
    }

    public function createArticle($title, $infoTambahan, $content, $photoPath = null)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO articles (title, infoTambahan, content, photo_path, published_date)
                VALUES (?, ?, ?, ?, NOW())
            ");

            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("ssss", $title, $infoTambahan, $content, $photoPath);
            $result = $stmt->execute();

            if (!$result) {
                error_log("Execute failed: " . $stmt->error);
            }

            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error in createArticle: " . $e->getMessage());
            return false;
        }
    }

    public function updateArticle($id, $title, $infoTambahan, $content, $photoPath = null)
    {
        try {
            if ($photoPath !== null) {
                $stmt = $this->db->prepare("
                    UPDATE articles 
                    SET title = ?, infoTambahan = ?, content = ?, photo_path = ?
                    WHERE id = ?
                ");
                $stmt->bind_param("ssssi", $title, $infoTambahan, $content, $photoPath, $id);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE articles 
                    SET title = ?, infoTambahan = ?, content = ?
                    WHERE id = ?
                ");
                $stmt->bind_param("sssi", $title, $infoTambahan, $content, $id);
            }

            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return false;
            }

            $result = $stmt->execute();

            if (!$result) {
                error_log("Execute failed: " . $stmt->error);
            }

            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error in updateArticle: " . $e->getMessage());
            return false;
        }
    }

    public function deleteArticle($id)
    {
        try {
            $article = $this->getArticleById($id);
            if ($article && !empty($article['photo_path']) && file_exists($article['photo_path'])) {
                unlink($article['photo_path']);
            }

            $stmt = $this->db->prepare("DELETE FROM articles WHERE id = ?");

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
            error_log("Error in deleteArticle: " . $e->getMessage());
            return false;
        }
    }
}
