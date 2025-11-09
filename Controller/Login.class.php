<?php
require_once "Controller.class.php";

class Login extends Controller {

    // Tampilkan form login
    public function index() {
        // Selalu tampilkan halaman login
        $this->view('Login.php');
    }

    // Verifikasi data login
    public function verifikasiData() {
        $this->startSession();
        
        // Pastikan method adalah POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=Login&m=index");
            exit;
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validasi input sederhana
        if (empty($username) || empty($password)) {
            header("Location: index.php?c=Login&m=index&error=1");
            exit;
        }

        try {
            $akun = $this->model('AkunKlien');
            $user = $akun->cekData($username, $password);

            if ($user && is_array($user)) {
                // Login berhasil
                $_SESSION['id'] = $user['id']; 
                $_SESSION['idKlien'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'] ?? '';
                $_SESSION['role'] = $user['role'] ?? 'client';
                
                header("Location: index.php?c=Dashboard&m=index");
                exit;
            } else {
                // Login gagal
                header("Location: index.php?c=Login&m=index&error=1");
                exit;
            }
        } catch (Exception $e) {
            // Error handling
            error_log("Login Error: " . $e->getMessage());
            header("Location: index.php?c=Login&m=index&error=1");
            exit;
        }
    }

    // Redirect ke registrasi
    public function arahkanKeRegistrasi() {
        header('Location: index.php?c=Register&m=index');
        exit;
    }

    // Logout - clear session
    public function logout() {
        $this->startSession();
        
        // Hapus semua session
        $_SESSION = array();
        
        // Hapus session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy session
        session_destroy();
        
        // Redirect ke login
        header('Location: index.php?c=Login&m=index');
        exit;
    }
}

