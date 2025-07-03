<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

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
  <title>Data Master</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
</head>
<body class="dashboard-page">
  <div class="dashboard-sidebar">
    <img src="img/MTN.webp" class="dashboard-logo">
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
      <input type="text" id="dashboard-search" placeholder="Search">
      <button type="button" onclick="window.open('print.php', '_blank')">Print</button>
      <span class="dashboard-total">Total Aset: <?= htmlspecialchars(number_format($total_aset,0,',','.')) ?></span>
    </div>

    <form id="formAksi">
      <div>
        <table class="dashboard-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama</th>
              <th>Kategori</th>
              <th>Tanggal Beli</th>
              <th>Harga Beli</th>
              <th>Umur Aset</th>
              <th>Penyusutan Per Tahun</th>
              <th>Nilai Buku Sekarang</th>
              <th>Sumber Dana</th>
              <th>Penanggung Jawab</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)):
            $harga = $row['harga_beli'];
            $umur = $row['umur_aset'];
            $penyusutan = $umur > 0 ? $harga / $umur : 0;
            
            $tahun_beli = date('Y', strtotime($row['tanggal_beli']));
            $tahun_sekarang = date('Y');
            $pakai = max(0, $tahun_sekarang - $tahun_beli);
            $nilai_buku = max(0, $harga - ($penyusutan * $pakai));
            ?>
            
            <tr>
              <td><?= htmlspecialchars($row['id_aset']) ?></td>
              <td><?= htmlspecialchars($row['nama_aset']) ?></td>
              <td><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></td>
              <td><?= htmlspecialchars($row['tanggal_beli']) ?></td>
              <td><?= number_format($harga, 0, ',', '.') ?></td>
              <td><?= $umur ?> tahun</td>
              <td><?= number_format($penyusutan, 0, ',', '.') ?></td>
              <td><?= number_format($nilai_buku, 0, ',', '.') ?></td>
              <td><?= htmlspecialchars($row['sumber_dana']) ?></td>
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

    searchInput.addEventListener('input', function () {
      const keyword = this.value;
      const xhr = new XMLHttpRequest();
      xhr.open('GET', 'search.php?search=' + encodeURIComponent(keyword) + '&page=datamaster', true);
      xhr.onload = function () {
        if (xhr.status === 200) {
          tableBody.innerHTML = xhr.responseText;
        }
      };
      xhr.send();
    });
  </script>
</body>
</html>