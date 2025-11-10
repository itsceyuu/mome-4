<?php
require_once "Controller.class.php";

class Articles extends Controller
{

    public function index()
    {
        $this->startSession();

        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        $articlesModel = $this->model('ArticlesModel');
        $search = $_GET['search'] ?? '';

        if (!empty($search)) {
            $articles = $articlesModel->searchArticles($search);
        } else {
            $articles = $articlesModel->getAllArticles();
        }

        $pageTitle = "Articles Finance";
        $activePage = "articles";

        $this->view('Articles.php', [
            'username' => $_SESSION['username'] ?? 'User',
            'userRole' => $_SESSION['role'] ?? 'client',
            'articles' => $articles,
            'search' => $search
        ]);
    }

    public function detail()
    {
        $this->startSession();

        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        $id = intval($_GET['id'] ?? 0);

        if ($id <= 0) {
            header("Location: index.php?c=Articles&m=index");
            exit;
        }

        $articlesModel = $this->model('ArticlesModel');
        $article = $articlesModel->getArticleById($id);

        if (!$article) {
            $_SESSION['error'] = 'Article not found!';
            header("Location: index.php?c=Articles&m=index");
            exit;
        }

        $pageTitle = $article['title'];
        $activePage = "articles";

        $this->view('ArticleDetail.php', [
            'username' => $_SESSION['username'] ?? 'User',
            'userRole' => $_SESSION['role'] ?? 'client',
            'article' => $article
        ]);
    }

    public function create()
    {
        $this->startSession();

        if (!isset($_SESSION['idKlien']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied! Admin only.';
            header("Location: index.php?c=Articles&m=index");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $infoTambahan = trim($_POST['infoTambahan'] ?? '');
            $content = trim($_POST['content'] ?? '');

            if (empty($title)) {
                $_SESSION['error'] = 'Title is required!';
                header("Location: index.php?c=Articles&m=create");
                exit;
            }

            $photoPath = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $photoPath = $this->uploadPhoto($_FILES['photo']);
                if (!$photoPath) {
                    $_SESSION['error'] = 'Failed to upload photo!';
                    header("Location: index.php?c=Articles&m=create");
                    exit;
                }
            }

            try {
                $articlesModel = $this->model('ArticlesModel');
                $result = $articlesModel->createArticle($title, $infoTambahan, $content, $photoPath);

                if ($result) {
                    $_SESSION['success'] = 'Article created successfully!';
                } else {
                    $_SESSION['error'] = 'Failed to create article!';
                    if ($photoPath && file_exists($photoPath)) {
                        unlink($photoPath);
                    }
                }
            } catch (Exception $e) {
                error_log("Error creating article: " . $e->getMessage());
                $_SESSION['error'] = 'An error occurred while creating the article.';
                if ($photoPath && file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }

            header("Location: index.php?c=Articles&m=index");
            exit;
        } else {
            $pageTitle = "Create Article";
            $activePage = "articles";

            $this->view('ArticleForm.php', [
                'username' => $_SESSION['username'] ?? 'User',
                'userRole' => $_SESSION['role'] ?? 'client',
                'pageTitle' => $pageTitle,
                'formAction' => 'create',
                'article' => null
            ]);
        }
    }

    public function edit()
    {
        $this->startSession();

        if (!isset($_SESSION['idKlien']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied! Admin only.';
            header("Location: index.php?c=Articles&m=index");
            exit;
        }

        $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error'] = 'Invalid article ID!';
            header("Location: index.php?c=Articles&m=index");
            exit;
        }

        $articlesModel = $this->model('ArticlesModel');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $infoTambahan = trim($_POST['infoTambahan'] ?? '');
            $content = trim($_POST['content'] ?? '');

            if (empty($title)) {
                $_SESSION['error'] = 'Title is required!';
                header("Location: index.php?c=Articles&m=edit&id=" . $id);
                exit;
            }

            $article = $articlesModel->getArticleById($id);
            if (!$article) {
                $_SESSION['error'] = 'Article not found!';
                header("Location: index.php?c=Articles&m=index");
                exit;
            }

            $photoPath = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $photoPath = $this->uploadPhoto($_FILES['photo']);
                if (!$photoPath) {
                    $_SESSION['error'] = 'Failed to upload photo!';
                    header("Location: index.php?c=Articles&m=edit&id=" . $id);
                    exit;
                }

                if (!empty($article['photo_path']) && file_exists($article['photo_path'])) {
                    unlink($article['photo_path']);
                }
            }

            try {
                $result = $articlesModel->updateArticle($id, $title, $infoTambahan, $content, $photoPath);

                if ($result) {
                    $_SESSION['success'] = 'Article updated successfully!';
                } else {
                    $_SESSION['error'] = 'Failed to update article!';
                }
            } catch (Exception $e) {
                error_log("Error updating article: " . $e->getMessage());
                $_SESSION['error'] = 'An error occurred while updating the article.';
            }

            header("Location: index.php?c=Articles&m=index");
            exit;
        } else {
            $article = $articlesModel->getArticleById($id);

            if (!$article) {
                $_SESSION['error'] = 'Article not found!';
                header("Location: index.php?c=Articles&m=index");
                exit;
            }

            $pageTitle = "Edit Article";
            $activePage = "articles";

            $this->view('ArticleForm.php', [
                'username' => $_SESSION['username'] ?? 'User',
                'userRole' => $_SESSION['role'] ?? 'client',
                'pageTitle' => $pageTitle,
                'formAction' => 'edit',
                'article' => $article
            ]);
        }
    }

    public function delete()
    {
        $this->startSession();

        if (!isset($_SESSION['idKlien']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied! Admin only.';
            header("Location: index.php?c=Articles&m=index");
            exit;
        }

        $id = intval($_POST['id'] ?? $_GET['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error'] = 'Invalid article ID!';
            header("Location: index.php?c=Articles&m=index");
            exit;
        }

        try {
            $articlesModel = $this->model('ArticlesModel');
            $result = $articlesModel->deleteArticle($id);

            if ($result) {
                $_SESSION['success'] = 'Article deleted successfully!';
            } else {
                $_SESSION['error'] = 'Failed to delete article!';
            }
        } catch (Exception $e) {
            error_log("Error deleting article: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while deleting the article.';
        }

        header("Location: index.php?c=Articles&m=index");
        exit;
    }

    private function uploadPhoto($file)
    {
        $uploadDir = 'uploads/articles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        $maxSize = 5 * 1024 * 1024;

        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        if ($file['size'] > $maxSize) {
            return false;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'article_' . time() . '_' . uniqid() . '.' . $extension;
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $targetPath;
        }

        return false;
    }
}
