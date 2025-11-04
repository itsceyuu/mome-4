<?php

class Login extends Controller {

    // Langkah 1 & 2: tampilkan form login
    public function index() {
        $this->view('Login.php');
    }

    // Langkah 3, 4, 5: verifikasi data login
    public function verifikasiData() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Panggil model AkunKlien
        $akun = $this->model('AkunKlien');

        // Cek data login di database
        $status = $akun->cekData($username, $password);

        if ($status === "valid") {
            // Jika login berhasil → tampilkan homepage
            session_start();
            $_SESSION['username'] = $username;
            $this->view('Homepage.php', ['username' => $username]);
        } else {
            // Jika gagal → tampilkan pesan error di halaman login
            $this->view('Login.php', ['error' => 'Invalid username or password']);
        }
    }

    // Alur alternatif: pengguna belum punya akun
    public function arahkanKeRegistrasi() {
        header('Location: index.php?c=Register&m=index');
        exit;
    }
}
