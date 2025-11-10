<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "mome";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$item_name = $_POST['item_name'] ?? '';
$description = $_POST['description'] ?? '';

if ($item_name !== '') {
    $stmt = $conn->prepare("INSERT INTO wishlist (item_name, description, date_added) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $item_name, $description);
    if ($stmt->execute()) {
        echo "Data berhasil ditambahkan!";
    } else {
        echo "Gagal menambahkan data.";
    }
    $stmt->close();
} else {
    echo "Data tidak lengkap.";
}

$conn->close();
?>
