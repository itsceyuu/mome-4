<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "mome";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_POST['id'])) {
  $id = intval($_POST['id']);

  $sql = "DELETE FROM wishlist WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    echo "success";
  } else {
    echo "error";
  }

  $stmt->close();
}

$conn->close();
?>
