<?php
require_once "Controller.class.php";

class Dashboard extends Controller {

    public function index() {
        $this->startSession();

        if (!isset($_SESSION['idKlien'])) {
            header("location:index.php?c=Login&m=index");
            return;
        }

        $idKlien = $_SESSION['idKlien'];
        $username = $_SESSION['username'];

        $dashboardModel = $this->model('HomepageModel');
        $data = $dashboardModel->getHomepageData($idKlien);

        $pageTitle = "Dashboard";
        $activePage = "dashboard";

        include("./View/Dashboard.php");
    }

    public function navigate($menu = null) {
        $this->startSession();
        
        // Pastikan user sudah login
        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }
        
        if ($menu === null && isset($_GET['menu'])) {
            $menu = $_GET['menu'];
        }

        switch ($menu) {
            case 'recap':
                header('Location: index.php?c=RecapController&m=index');
                exit;
            case 'expenses':
                header('Location: index.php?c=ExpenseController&m=index');
                exit;
            case 'goals':
                header('Location: index.php?c=Goals&m=index');
                exit;
            case 'wishlist':
                header('Location: index.php?c=Wishlist&m=index');
                exit;
            case 'articles':
                header('Location: index.php?c=Articles&m=index');
                exit;
            case 'dashboard':
                header('Location: index.php?c=Dashboard&m=index');
                exit;
            default:
                header('Location: index.php?c=Dashboard&m=index');
                exit;
        }
    }
}
?>