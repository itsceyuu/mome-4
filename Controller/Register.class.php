<?php
require_once "Controller.class.php";

class Register extends Controller {

    // Langkah 1: tampilkan form registrasi
    public function index() {
        $this->view('Register.php');
    }

    // Langkah 2–7: proses registrasi
    public function proses() {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Panggil model AkunKlienModel
        $akun = $this->model('AkunKlien');

        // Cek apakah username atau email sudah terdaftar
        if ($akun->cekEmail($email) || $akun->cekUsername($username)) {
            $this->view('Register.php', ['error_registered' => 'Your account is already registered']);
            return;
        }

        // Simpan data baru
        $status = $akun->simpanData($username, $email, $password);

        // Jika berhasil → arahkan ke halaman login
        if ($status) {
            header('Location: index.php?c=Login&m=index&msg=register_success');
            exit;
        } else {
            // Jika gagal simpan
            $this->view('Register.php', ['error' => 'Registration failed, try again.']);
        }
    }

}
