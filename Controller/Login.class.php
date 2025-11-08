<?php

class Login extends Controller {

    // Langkah 1 & 2: tampilkan form login
    public function index() {
        $this->view('Login.php');
    }

    // Langkah 3, 4, 5: verifikasi data login
    // public function verifikasiData() {
    //     $username = $_POST['username'] ?? '';
    //     $password = $_POST['password'] ?? '';

    //     // Panggil model AkunKlien
    //     $akun = $this->model('AkunKlien');

    //     // Cek data login di database
    //     $status = $akun->cekData($username, $password);

    //     if ($status === "valid") {
    //         // Jika login berhasil → tampilkan homepage
    //         // session_start();
    //         $_SESSION['username'] = $username;
    //         $this->view('Dashboard.php', ['username' => $username]);
    //     } else {
    //         // Jika gagal → tampilkan pesan error di halaman login
    //         $this->view('Login.php', ['error' => 'Invalid username or password']);
    //     }
    // }
    public function verifikasiData() {
        $this->startSession(); // menggunakan helper method (OOP)
        
        // Pastikan method adalah POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=Login&m=index");
            exit;
        }
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validasi input
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
                $_SESSION['idKlien'] = $user['id']; // Untuk kompatibilitas dengan Dashboard
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

    // Alur alternatif: pengguna belum punya akun
    public function arahkanKeRegistrasi() {
        header('Location: index.php?c=Register&m=index');
    exit;
    }
}
// class Login extends Controller {

//     // Langkah 1 & 2: tampilkan form login
//     public function index() {
//         $this->view('Login.php');
//     }

//     // Langkah 3, 4, 5: verifikasi data login
//     public function verifikasiData() {
        // $username = $_POST['username'] ?? '';
        // $password = $_POST['password'] ?? '';

        // // Panggil model AkunKlien
        // $akun = $this->model('AkunKlien');

        // Cek data login di database
        // $status = $akun->cekData($username, $password);
        // echo "Status login: $status<br>";
        // if ($status === "valid") {
        //     // Jika login berhasil → tampilkan homepage
        //     session_start();
        //     $_SESSION['username'] = $username;
        //     $this->view('Dashboard.php', ['username' => $username]);
        // } else {
        //     // Jika gagal → tampilkan pesan error di halaman login
        //     $this->view('Login.php', ['error' => 'Invalid username or password']);
        // }
        // if ($status === "valid") {
        //     session_start();
        //     $_SESSION['username'] = $username;
        //     header("Location: index.php?c=Dashboard&m=index");
        //     exit;
        // } else {
        //     $this->view('Login.php', ['error' => 'Invalid username or password']);
        // }

    //     $user = $akun->cekData($username, $password);

    //     // Jika login berhasil
    //     if ($user !== null) {
    //         session_start();
    //         $_SESSION['id'] = $user['id'];
    //         $_SESSION['username'] = $user['username'];
    //         $_SESSION['role'] = $user['role'];

    //         // ✅ Redirect ke Dashboard controller method index
    //         header("Location: index.php?c=Dashboard&m=index");
    //         exit;
    //     } else {
    //         // Jika gagal login, tampilkan error
    //         $this->view('Login.php', ['error' => 'Invalid username or password']);
    //     }
    // }

    // // Alur alternatif: pengguna belum punya akun
    // public function arahkanKeRegistrasi() {
    //     header('Location: index.php?c=Register&m=index');
    //     exit;
        // Langkah 3, 4, 5: verifikasi data login
    //     $username = $_POST['username'] ?? '';
    //     $password = $_POST['password'] ?? '';

    //     // Panggil model AkunKlien
    //     $akun = $this->model('AkunKlien');

    //     // Cek data login di database
    //     $status = $akun->cekData($username, $password);

    //     if ($status === "valid") {
    //         // Jika login berhasil → tampilkan homepage
    //         // session_start();
    //         $_SESSION['username'] = $username;
    //         $this->view('Dashboard.php', ['username' => $username]);
    //     } else {
    //         // Jika gagal → tampilkan pesan error di halaman login
    //         $this->view('Login.php', ['error' => 'Invalid username or password']);
    //     }
    // }

    // Alur alternatif: pengguna belum punya akun
//     public function arahkanKeRegistrasi() {
//         header('Location: index.php?c=Register&m=index');
//     exit;
//     }
// }

