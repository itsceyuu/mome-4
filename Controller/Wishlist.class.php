<?php
require_once "Controller.class.php";

class Wishlist extends Controller
{

    public function index()
    {
        $this->startSession();

        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        $idKlien = $_SESSION['idKlien'];
        $wishlistModel = $this->model('WishlistModel');

        $wishlistItems = $wishlistModel->getAllWishlist($idKlien);

        $pageTitle = "Wishlist";
        $activePage = "wishlist";

        $this->view('Wishlist.php', [
            'username' => $_SESSION['username'] ?? 'User',
            'wishlistItems' => $wishlistItems
        ]);
    }

    public function create()
    {
        $this->startSession();

        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idKlien = $_SESSION['idKlien'];
            $itemName = trim($_POST['item_name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (empty($itemName)) {
                $_SESSION['error'] = 'Item name is required!';
                header("Location: index.php?c=Wishlist&m=create");
                exit;
            }

            try {
                $wishlistModel = $this->model('WishlistModel');
                $result = $wishlistModel->createWishlist($idKlien, $itemName, $description);

                if ($result) {
                    $_SESSION['success'] = 'Wishlist item created successfully!';
                } else {
                    $_SESSION['error'] = 'Failed to create wishlist item!';
                }
            } catch (Exception $e) {
                error_log("Error creating wishlist: " . $e->getMessage());
                $_SESSION['error'] = 'An error occurred while creating the wishlist item.';
            }

            header("Location: index.php?c=Wishlist&m=index");
            exit;
        } else {
            $pageTitle = "Add New Wishlist";
            $activePage = "wishlist";

            $this->view('WishlistForm.php', [
                'username' => $_SESSION['username'] ?? 'User',
                'pageTitle' => $pageTitle,
                'formAction' => 'create',
                'item' => null
            ]);
        }
    }

    public function edit()
    {
        $this->startSession();

        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error'] = 'Invalid wishlist ID!';
            header("Location: index.php?c=Wishlist&m=index");
            exit;
        }

        $wishlistModel = $this->model('WishlistModel');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemName = trim($_POST['item_name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (empty($itemName)) {
                $_SESSION['error'] = 'Item name is required!';
                header("Location: index.php?c=Wishlist&m=edit&id=" . $id);
                exit;
            }

            try {
                $item = $wishlistModel->getWishlistById($id);
                if (!$item || $item['user_id'] != $_SESSION['idKlien']) {
                    $_SESSION['error'] = 'You do not have permission to edit this item!';
                    header("Location: index.php?c=Wishlist&m=index");
                    exit;
                }

                $result = $wishlistModel->updateWishlist($id, $itemName, $description);

                if ($result) {
                    $_SESSION['success'] = 'Wishlist item updated successfully!';
                } else {
                    $_SESSION['error'] = 'Failed to update wishlist item!';
                }
            } catch (Exception $e) {
                error_log("Error updating wishlist: " . $e->getMessage());
                $_SESSION['error'] = 'An error occurred while updating the wishlist item.';
            }

            header("Location: index.php?c=Wishlist&m=index");
            exit;
        } else {
            $item = $wishlistModel->getWishlistById($id);

            if (!$item || $item['user_id'] != $_SESSION['idKlien']) {
                $_SESSION['error'] = 'Wishlist item not found!';
                header("Location: index.php?c=Wishlist&m=index");
                exit;
            }

            $pageTitle = "Edit Wishlist";
            $activePage = "wishlist";

            $this->view('WishlistForm.php', [
                'username' => $_SESSION['username'] ?? 'User',
                'pageTitle' => $pageTitle,
                'formAction' => 'edit',
                'item' => $item
            ]);
        }
    }

    public function delete()
    {
        $this->startSession();

        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        $id = intval($_POST['id'] ?? $_GET['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error'] = 'Invalid wishlist ID!';
            header("Location: index.php?c=Wishlist&m=index");
            exit;
        }

        try {
            $wishlistModel = $this->model('WishlistModel');

            $item = $wishlistModel->getWishlistById($id);
            if (!$item || $item['user_id'] != $_SESSION['idKlien']) {
                $_SESSION['error'] = 'You do not have permission to delete this item!';
                header("Location: index.php?c=Wishlist&m=index");
                exit;
            }

            $result = $wishlistModel->deleteWishlist($id);

            if ($result) {
                $_SESSION['success'] = 'Wishlist item deleted successfully!';
            } else {
                $_SESSION['error'] = 'Failed to delete wishlist item!';
            }
        } catch (Exception $e) {
            error_log("Error deleting wishlist: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while deleting the wishlist item.';
        }

        header("Location: index.php?c=Wishlist&m=index");
        exit;
    }
}
