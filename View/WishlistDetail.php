<?php
// --- koneksi ke database ---
$host = "localhost";
$user = "root";
$pass = "";
$db   = "mome";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// --- ambil data berdasarkan idWishlist di URL ---
if (!isset($_GET['idWishlist'])) {
  die("ID wishlist tidak ditemukan!");
}

$idWishlist = intval($_GET['idWishlist']);
$sql = "SELECT * FROM wishlist WHERE id = $idWishlist";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
  die("Data wishlist tidak ditemukan!");
}

$item = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Wishlist Detail - <?= htmlspecialchars($item['item_name']) ?></title>
  <link rel="stylesheet" href="WishlistDetail.css">
</head>
<body>

<div class="wishlist-container">
  <div class="wishlist-header">
    <h2>Wishlist</h2>
    <p>Keep track of your dream items easily!<br>Add your wishlist to monitor the things you plan to buy in the future.</p>
  </div>

  <div class="wishlist-card">
    <div class="wishlist-card-header">
      <h3><?= htmlspecialchars($item['item_name']) ?></h3>
      <button class="btn-back" onclick="window.location.href='Wishlist.php'">←</button>
    </div>

    <div class="wishlist-description">
      <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>
    </div>

    <div class="wishlist-actions">
      <button class="btn-edit" onclick="openEditPopup()">Edit</button>
      <button class="btn-delete" onclick="openDeletePopup()">Delete</button>
    </div>
  </div>
</div>

<!-- Popup Edit -->
<div id="popupEdit" class="popup-overlay">
  <div class="popup-box">
    <button class="close-btn" onclick="closeEditPopup()">×</button>
    <div class="popup-content">
      <h3>EDIT WISHLIST</h3>
      <input type="hidden" id="editId" value="<?= $item['id'] ?>">

      <label for="editTitle" class="popup-label">Title<span class="required">*</span></label>
      <input type="text" id="editTitle" class="popup-input" value="<?= htmlspecialchars($item['item_name']) ?>">

      <label for="editDescription" class="popup-label">Description</label>
      <textarea id="editDescription" class="popup-textarea"><?= htmlspecialchars($item['description']) ?></textarea>
    </div>
    <div class="popup-actions">
      <button id="confirmEditBtn" class="confirm-btn">Confirm</button>
    </div>
  </div>
</div>

<!-- Popup Delete -->
<div id="popupDelete" class="popup-overlay">
  <div class="popup-box">
    <button class="close-btn" onclick="closeDeletePopup()">×</button>
    <div class="popup-content">
      <h3>REMOVE FROM WISHLIST?</h3>
      <div class="popup-icon">❌</div>
    </div>
    <div class="popup-actions">
      <button id="confirmDeleteBtn" class="confirm-btn">Confirm</button>
    </div>
  </div>
</div>

<script>
// === EDIT ===
function openEditPopup() {
  document.getElementById('popupEdit').style.display = 'flex';
}
function closeEditPopup() {
  document.getElementById('popupEdit').style.display = 'none';
}

document.getElementById('confirmEditBtn').addEventListener('click', () => {
  const id = document.getElementById('editId').value;
  const title = document.getElementById('editTitle').value.trim();
  const description = document.getElementById('editDescription').value.trim();

  if (!title) {
    alert("Title tidak boleh kosong!");
    return;
  }

  fetch('edit_wishlist.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `id=${encodeURIComponent(id)}&item_name=${encodeURIComponent(title)}&description=${encodeURIComponent(description)}`
  })
  .then(r => r.text())
  .then(d => {
    console.log("Respon edit:", d);
    closeEditPopup();
    setTimeout(() => location.reload(), 500);
  });
});

// === DELETE ===
function openDeletePopup() {
  document.getElementById('popupDelete').style.display = 'flex';
}
function closeDeletePopup() {
  document.getElementById('popupDelete').style.display = 'none';
}

document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
  const id = <?= $item['id'] ?>;
  fetch(`delete_wishlist.php?id=${id}`)
    .then(r => r.text())
    .then(d => {
      console.log("Respon delete:", d);
      closeDeletePopup();
      setTimeout(() => window.location.href = 'Wishlist.php', 500);
    });
});
</script>

</body>
</html>
