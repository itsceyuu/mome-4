<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "mome"; // ganti sesuai nama database kamu

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil semua data dari tabel wishlist
$sql = "SELECT id, item_name, description FROM wishlist";
$result = $conn->query($sql);

$wishlistData = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $wishlistData[] = $row;
    }
}

$conn->close(); 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mome - Wishlist</title>
    <link rel="stylesheet" href="Wishlistt.css">
</head>
<body>


<!-- Popup Tambah Wishlist -->
<div id="popupAdd" class="popup-overlay">
  <div class="popup-box">
    <button class="close-btn" onclick="closeAddPopup()">×</button>
    <div class="popup-content">
      <h3>ADD WISHLIST</h3>

      <label for="addTitle" class="popup-label">Title<span class="required">*</span></label>
      <input type="text" id="addTitle" placeholder="Enter title" class="popup-input">

      <label for="addDescription" class="popup-label">Description</label>
      <textarea id="addDescription" placeholder="Enter description" class="popup-textarea"></textarea>
    </div>
    <div class="popup-actions">
      <button id="confirmAddBtn" class="confirm-btn">Confirm</button>
    </div>
  </div>
</div>

<!-- Popup Edit Wishlist -->
<div id="popupEdit" class="popup-overlay">
  <div class="popup-box">
    <button class="close-btn" onclick="closeEditPopup()">×</button>
    <div class="popup-content">
      <h3>EDIT WISHLIST</h3>
      <input type="hidden" id="editId">

      <label for="editTitle" class="popup-label">Title<span class="required">*</span></label>
      <input type="text" id="editTitle" placeholder="Enter title" class="popup-input">

      <label for="editDescription" class="popup-label">Description</label>
      <textarea id="editDescription" placeholder="Enter description" class="popup-textarea"></textarea>
    </div>
    <div class="popup-actions">
      <button id="confirmEditBtn" class="confirm-btn">Confirm</button>
    </div>
  </div>
</div>

    <!-- Popup Konfirmasi Delete -->
<div id="popupConfirm" class="popup-overlay">
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



    <div class="wishlist-container">
        <div class="wishlist-header">
            <h2>Wishlist</h2>
            <p>Keep track of your dream items easily!<br>Add your wishlist to monitor the things you plan to buy in the future.</p>
        </div>
        <main>
            <div class="wishlist-table">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($wishlistData as $item): ?>
                        <tr onclick="rowClicked(<?= $item['id'] ?>)">
                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                            <td><?= htmlspecialchars($item['description']) ?></td>
                            <td class="actions" onclick="event.stopPropagation();">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn" data-id="<?= $item['id'] ?>">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <button class="add-btn">+</button>
        </main>
    </div>

    <script>
        function rowClicked(id) {
            window.location.href = 'WishlistDetail.php?idWishlist=' + id;
        }
    </script>
    

    
<script>
let selectedId = null;

// Tombol delete di tabel → buka popup
document.querySelectorAll('.delete-btn').forEach(button => {
  button.addEventListener('click', function () {
    selectedId = this.getAttribute('data-id');
    document.getElementById('popupConfirm').style.display = 'flex';
  });
});

// Tombol X → tutup popup
function closePopup() {
  document.getElementById('popupConfirm').style.display = 'none';
  selectedId = null;
}

// Tombol konfirmasi hapus
document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
  if (!selectedId) {
    console.log("ID tidak ada");
    return;
  }

  console.log("Menghapus ID:", selectedId);

  fetch(`delete_wishlist.php?id=${selectedId}`)
    .then(response => response.text())
    .then(data => {
      console.log("Respon dari server:", data);
      // Tutup popup
      document.getElementById('popupConfirm').style.display = 'none';
      // Refresh setelah 0.5 detik biar smooth
      setTimeout(() => location.reload(), 500);
    })
    .catch(error => console.error("Error:", error));
});
</script>

<script>
const popupAdd = document.getElementById('popupAdd');

// Klik tombol +
document.querySelector('.add-btn').addEventListener('click', () => {
  popupAdd.style.display = 'flex';
});

// Tutup popup
function closeAddPopup() {
  popupAdd.style.display = 'none';
  document.getElementById('addTitle').value = '';
  document.getElementById('addDescription').value = '';
}

// Klik tombol Confirm tambah
document.getElementById('confirmAddBtn').addEventListener('click', () => {
  const title = document.getElementById('addTitle').value.trim();
  const description = document.getElementById('addDescription').value.trim();

  if (!title) {
    alert("Title tidak boleh kosong!");
    return;
  }

  fetch('add_wishlist.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `item_name=${encodeURIComponent(title)}&description=${encodeURIComponent(description)}`
  })
  .then(response => response.text())
  .then(data => {
    console.log("Respon tambah:", data);
    closeAddPopup();
    setTimeout(() => location.reload(), 500);
  })
  .catch(err => console.error(err));
});
</script>

<script>
const popupEdit = document.getElementById('popupEdit');

// Buka popup edit dan isi data
document.querySelectorAll('.edit-btn').forEach(button => {
  button.addEventListener('click', function() {
    const row = this.closest('tr');
    const id = row.querySelector('.delete-btn').getAttribute('data-id');
    const title = row.cells[0].innerText;
    const desc = row.cells[1].innerText;

    document.getElementById('editId').value = id;
    document.getElementById('editTitle').value = title;
    document.getElementById('editDescription').value = desc;

    popupEdit.style.display = 'flex';
  });
});

// Tutup popup edit
function closeEditPopup() {
  popupEdit.style.display = 'none';
}

// Tombol Save ditekan
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
  .then(response => response.text())
  .then(data => {
    console.log("Respon edit:", data);
    closeEditPopup();
    setTimeout(() => location.reload(), 500);
  })
  .catch(err => console.error(err));
});
</script>


</body>
</html>


