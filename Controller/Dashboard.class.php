<?php
require_once "Controller.class.php";

class Dashboard extends Controller {

    public function index() {
        session_start();
        if (!isset($_SESSION['id'])) {
            header("location:index.php?c=Login&m=index");
            return;
        }
        $this->view('Dashboard.php');
    }
}
?>
