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
    if ($menu === null && isset($_GET['menu'])) {
        $menu = $_GET['menu'];
    }

    switch ($menu) {
        case 'recap':
            header('Location: index.php?c=Recap&m=index');
            break;
        case 'expenses':
            header('Location: index.php?c=ExpenseController&m=index');
            break;
        case 'goals':
            header('Location: index.php?c=Goals&m=index');
            break;
        case 'wishlist':
            header('Location: index.php?c=Wishlist&m=index');
            break;
        case 'articles':
            header('Location: index.php?c=Articles&m=index');
            break;
        default:
            header('Location: index.php?c=Dashboard&m=index');
            break;}
}
}
?>