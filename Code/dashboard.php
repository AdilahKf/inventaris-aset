<?php
session_start();
include 'db.php';

// Cek jika user belum login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil data aset + nama penanggung jawab (JOIN)
$sql = "SELECT aset.*, kategori.nama_kategori, penanggung_jawab.nama_pj
        FROM aset 
        LEFT JOIN kategori ON aset.id_kategori = kategori.id_kategori
        LEFT JOIN penanggung_jawab ON aset.id_pj = penanggung_jawab.id_pj";

$result = mysqli_query($conn, $sql);

$count = mysqli_query($conn, "SELECT COUNT(*) as total FROM aset");
$row = mysqli_fetch_assoc($count);
$total_aset = $row['total'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" href="style.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet"/>
</head>

<body class="dashboard-page">
  <div class="dashboard-sidebar">
    <img src="img/MTN.webp" class="dashboard-logo"/>
    <h3>Inventaris Management</h3>
    <a href="dashboard.php">Dashboard</a>
    <a href="datamaster.php">Laporan</a>
    <?php if ($_SESSION['role'] === 'admin'): ?>
      <a href="export.php">Ekspor Data</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
  </div>

  <div class="dashboard-table-container">
    <h2 class="dashboard-table-title">Hello, <?= htmlspecialchars($_SESSION['nama_user']) ?>!</h2>
    <h3 class="dashboard-table-subtitle">Data Aset</h3>

    <div class="dashboard-navbar">
      <input type="text" id="dashboard-search" placeholder="Search" />
      <button type="button" id="btnTambah" onclick="window.location.href='tambah.php'">Tambah</button>
      <button type="button" id="btnEdit" disabled>Edit</button>
      <?php if ($_SESSION['role'] === 'admin'): ?>
        <button type="button" id="btnHapus" disabled>Hapus</button>
      <?php endif; ?>
      <span class="dashboard-total">Total Aset: <?= htmlspecialchars(number_format($total_aset,0,',','.')) ?></span>
    </div>

    <form id="formAksi">
      <div>
        <table class="dashboard-table">
          <thead>
            <tr>
              <th>Pilih</th>
              <th>ID</th>
              <th>Nama</th>
              <th>Kategori</th>
              <th>Kondisi</th>
              <th>Status</th>
              <th>Sumber Dana</th>
              <th>Tanggal Beli</th>
              <th>Umur Aset</th>
              <th>Harga Beli</th>
              <th>Penanggung Jawab</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><input type="checkbox" name="pilih[]" value="<?= $row['id_aset'] ?>" class="dashboard-checkbox"></td>
                <td><?= htmlspecialchars($row['id_aset']) ?></td>
                <td><?= htmlspecialchars($row['nama_aset']) ?></td>
                <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                <td><?= htmlspecialchars($row['kondisi']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['sumber_dana']) ?></td>
                <td><?= htmlspecialchars($row['tanggal_beli']) ?></td>
                <td><?= htmlspecialchars(number_format($row['umur_aset'], 0, ',', '.')) ?></td>
                <td><?= htmlspecialchars(number_format($row['harga_beli'], 0, ',', '.')) ?></td>
                <td><?= htmlspecialchars($row['nama_pj'] ?? '-') ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </form>
  </div>

  <script>
  const searchInput = document.getElementById('dashboard-search');
  const tableBody = document.querySelector('.dashboard-table tbody');
  const btnEdit = document.getElementById('btnEdit');
  const btnHapus = document.getElementById('btnHapus');

  function updateButtons() {
    const checked = document.querySelectorAll('.dashboard-checkbox:checked');
    btnHapus && (btnHapus.disabled = checked.length === 0);
    btnEdit.disabled = checked.length !== 1;
  }

  function bindCheckboxEvents() {
    const checkboxes = document.querySelectorAll('.dashboard-checkbox');
    checkboxes.forEach(cb => cb.addEventListener('change', updateButtons));
    updateButtons();
  }

  searchInput.addEventListener('input', function () {
    const keyword = this.value;
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'search.php?search=' + encodeURIComponent(keyword) + '&page=dashboard', true);
    xhr.onload = function () {
      if (xhr.status === 200) {
        tableBody.innerHTML = xhr.responseText;
        bindCheckboxEvents(); // re-bind event setelah pencarian
      }
    };
    xhr.send();
  });

  bindCheckboxEvents();

  btnEdit.addEventListener('click', () => {
    const selected = document.querySelector('.dashboard-checkbox:checked');
    if (selected) {
      window.location.href = `edit.php?id_aset=${selected.value}`;
    }
  });
  
  btnHapus?.addEventListener('click', () => {
  const selected = document.querySelectorAll('.dashboard-checkbox:checked');
  if (selected.length > 0 && confirm('Yakin ingin menghapus data terpilih?')) {
    const formHapus = document.getElementById('formHapus');

    // Hapus input sebelumnya (biar gak dobel)
    formHapus.querySelectorAll('input[name="id_aset[]"]').forEach(e => e.remove());

    selected.forEach(cb => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'id_aset[]';
      input.value = cb.value;
      formHapus.appendChild(input);
    });

    formHapus.submit();
  }
  });
</script>

<form id="formHapus" method="POST" action="hapus.php" style="display: none;">
  <input type="hidden" name="hapus" value="1">
</form>

</body>
</html>
