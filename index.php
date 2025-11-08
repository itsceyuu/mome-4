<?php

    $c = $_GET['c'] ?? 'Login';
    $m = $_GET['m'] ?? 'index';
    
    require_once("Controller/Controller.class.php");

    // Cek controller
    $path = "Controller/$c.class.php";
    if (!file_exists($path)) {
        die("Controller '$c' tidak ditemukan!");
    }

    require_once($path);

    // Cek class
    if (!class_exists($c)) {
        die("Class '$c' tidak ditemukan di $path!");
    }

    // Buat objek controller
    $controller = new $c();

    // Cek method
    if (!method_exists($controller, $m)) {
        die("Method '$m' tidak ditemukan di controller '$c'!");
    }

    // Jalankan method
    $controller->$m();
?>


