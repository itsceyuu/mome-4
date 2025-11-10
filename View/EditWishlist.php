<?php
include 'config.php';

$id = $_POST['id'];
$title = $_POST['item_name'];
$description = $_POST['description'];

if (empty($id) || empty($title)) {
    echo "ID atau Title kosong!";
    exit;
}

$stmt = $conn->prepare("UPDATE wishlist SET item_name = ?, description = ? WHERE id = ?");
$stmt->bind_param("ssi", $title, $description, $id);

if ($stmt->execute()) {
    echo "Data berhasil diperbarui!";
} else {
    echo "Gagal memperbarui data: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
