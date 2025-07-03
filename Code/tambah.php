<?php
include 'db.php';

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "inventaris");

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses form jika disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_aset = $_POST['id_aset'];
    $nama_aset = $_POST['nama_aset'];
    $tanggal_beli = $_POST['tanggal_beli'];
    $id_kategori = $_POST['id_kategori']; // berubah dari 'kategori'
    $harga_beli = $_POST['harga_beli'];
    $kondisi = $_POST['kondisi'];
    $umur_aset = $_POST['umur_aset'];
    $sumber_dana = $_POST['sumber_dana'];
    $status = $_POST['status'];
    $id_pj = $_POST['id_pj']; // berubah dari 'penanggung_jawab'

    $id_aset = $_POST['id_aset'] ?? null;
    if (!$id_aset) {
      $id_kategori = $_POST['id_kategori'];
      $query = "SELECT id_aset FROM aset WHERE id_aset LIKE '{$id_kategori}%' ORDER BY id_aset DESC LIMIT 1";
      $result = $conn->query($query);
      if ($result->num_rows > 0) {
        $last_id = $result->fetch_assoc()['id_aset'];
        $last_num = intval(substr($last_id, strlen($id_kategori)));
        $new_num = str_pad($last_num + 1, 4, "0", STR_PAD_LEFT);
      } else {
        $new_num = "0001";
      }
      $id_aset = $id_kategori . $new_num;
    }

    $sql = "INSERT INTO aset 
        (id_aset, nama_aset, tanggal_beli, id_kategori, harga_beli, kondisi, umur_aset, sumber_dana, status, id_pj)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssissssi", $id_aset, $nama_aset, $tanggal_beli, $id_kategori, $harga_beli, $kondisi, $umur_aset, $sumber_dana, $status, $id_pj);
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil ditambahkan!');window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah data!');</script>";
    }
    $stmt->close();
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Input Data Aset</title>
  <link rel="stylesheet" href="style.css">
</head>

<body class="tambah-page">
  <div class="tambah-blur-image"></div>
    <div class="tambah-form-container">
      <h2 class="tambah-form-title">Form Input Data Aset</h2>
      <form method="post" class="tambah-form">
        <div class="tambah-form-group">
          <label for="id-aset">ID Aset</label>
          <input type="text" id="id-aset" name="id_aset" readonly required>
        </div>
        <div class="tambah-form-group">
          <label for="nama-aset">Nama Aset</label>
          <input type="text" id="nama-aset" name="nama_aset" required>
        </div>
        <div class="tambah-form-group">
          <label for="tanggal-beli">Tanggal Beli</label>
          <input type="date" id="tanggal-beli" name="tanggal_beli" required>
        </div>
        <div class="tambah-form-group">
          <label for="kategori">Kategori</label>
          <select name="id_kategori" id="kategori" onchange="generateIDAset()">
            <option value="KD">Kendaraan</option>
            <option value="EL">Elektronik</option>
            <option value="GD">Gedung</option>
            <option value="PK">Peralatan Kantor</option>
            <option value="LN">Lain</option>
          </select>
        </div>
        <div class="tambah-form-group">
          <label for="umur_aset">Umur Aset (tahun)</label>
          <input type="number" name="umur_aset" id="umur_aset" required placeholder="Contoh: 5 Tahun">
        </div>
        <div class="tambah-form-group">
          <label for="harga-beli">Harga Beli</label>
          <input type="number" id="harga-beli" name="harga_beli" min="0" step="1" required>
        </div>
        <div class="tambah-form-group">
          <label>Kondisi</label>
          <div class="tambah-radio-group">
            <label><input type="radio" name="kondisi" value="Baik" required> Baik</label>
            <label><input type="radio" name="kondisi" value="Rusak Ringan" required> Rusak Ringan</label>
            <label><input type="radio" name="kondisi" value="Rusak Berat" required> Rusak Berat</label>
          </div>
        </div>
        <div class="tambah-form-group">
          <label for="sumber-dana">Sumber Dana</label>
          <input type="text" id="sumber-dana" name="sumber_dana" required>
        </div>
        <div class="tambah-form-group">
          <label for="status">Status</label>
          <select name="status" class="tambah-select" required>
            <option value="Beroperasi">Beroperasi</option>
            <option value="Tidak Beroperasi">Tidak Beroperasi</option>
            <option value="Perbaikan">Dalam Perbaikan</option>
            <option value="Pensiun/Dibuang">Pensiun/Dibuang</option>
          </select>
        </div>
        <div class="tambah-form-group">
          <label for="id_pj">Penanggung Jawab</label>
          <select id="id_pj" name="id_pj" required>
            <option value="">-- Pilih Penanggung Jawab --</option>
            <?php
            include 'db.php'; // pastikan koneksi jalan
            $result = $conn->query("SELECT id_pj, nama_pj FROM penanggung_jawab");
            while ($row = $result->fetch_assoc()) {
              echo "<option value='{$row['id_pj']}'>{$row['nama_pj']}</option>";
            }
            ?>
            </select>
          </div>
          <div class="tambah-button-group">
            <button type="submit" class="tambah-btn-submit">Submit</button>
            <a href="dashboard.php" class="tambah-btn-cancel">Batal</a>
          </div>
        </form>
      </div>
<script>
function generateIDAset() {
  const kategori = document.getElementById('kategori').value;
  fetch('getID.php?id_kategori=' + kategori)
    .then(res => res.text())
    .then(data => {
      document.getElementById('id-aset').value = data;
    });
}
// Panggil langsung saat halaman load
window.onload = generateIDAset;
</script>
</body>
</html>