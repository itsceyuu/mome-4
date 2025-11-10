<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "mome";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "delete") {
  $id = intval($_POST["idWishlist"]);
  $sql = "DELETE FROM wishlist WHERE idWishlist = $id";

  if ($conn->query($sql) === TRUE) {
    echo "Item berhasil dihapus!";
  } else {
    echo "Gagal menghapus item: " . $conn->error;
  }
}

$conn->close();
?>
