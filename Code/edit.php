<?php
include 'db.php';
session_start();

// Ambil ID dari URL
$id = isset($_GET['id_aset']) ? $_GET['id_aset'] : (isset($_GET['id']) ? $_GET['id'] : null);

if (!$id) {
    echo "<script>alert('ID tidak valid!');window.location='dashboard.php';</script>";
    exit;
}

// Ambil data aset berdasarkan ID
$query = "SELECT * FROM aset WHERE id_aset = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!');window.location='dashboard.php';</script>";
    exit;
}

// Ambil data kategori dan penanggung jawab
$kategori_result = mysqli_query($conn, "SELECT * FROM kategori");
$pj_result = mysqli_query($conn, "SELECT * FROM penanggung_jawab");

// Proses update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_aset = $_POST['nama_aset'];
    $tanggal_beli = $_POST['tanggal_beli'];
    $id_kategori = $_POST['id_kategori'];
    $harga_beli = $_POST['harga_beli'];
    $kondisi = $_POST['kondisi'];
    $sumber_dana = $_POST['sumber_dana'];
    $status = $_POST['status'];
    $id_pj = $_POST['id_pj'];
    $umur_aset = $_POST['umur_aset'];

    $update_sql = "UPDATE aset SET nama_aset=?, tanggal_beli=?, id_kategori=?, harga_beli=?, kondisi=?, sumber_dana=?, status=?, id_pj=?, umur_aset=? WHERE id_aset=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssisssiss", $nama_aset, $tanggal_beli, $id_kategori, $harga_beli, $kondisi, $sumber_dana, $status, $id_pj, $umur_aset, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil diupdate!');window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Data Aset</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="tambah-page">
  <div class="tambah-blur-image"></div>
    <div class="tambah-form-container">
      <h2 class="tambah-form-title">Edit Data Aset</h2>
      <form method="post" class="tambah-form">
        <div class="tambah-form-group">
          <label for="id-aset">ID Aset</label>
          <input type="text" name="id_aset" value="<?php echo htmlspecialchars($data['id_aset']) ?>" required readonly>
        </div>
        <div class="tambah-form-group">
          <label for="nama-aset">Nama Aset</label>
          <input type="text" name="nama_aset" value="<?php echo htmlspecialchars($data['nama_aset']) ?>" required>
        </div>

        <div class="tambah-form-group">
          <label for="tanggal-beli">Tanggal Beli</label>
          <input type="date" name="tanggal_beli" value="<?php echo htmlspecialchars($data['tanggal_beli']) ?>" required>
        </div>

        <div class="tambah-form-group">
          <label for="id_kategori">Kategori</label>
          <select name="id_kategori" required>
            <?php while ($kategori = mysqli_fetch_assoc($kategori_result)): ?>
              <option value="<?php echo $kategori['id_kategori'] ?>" <?php echo $data['id_kategori'] == $kategori['id_kategori'] ? 'selected' : '' ?>>
                <?php echo htmlspecialchars($kategori['nama_kategori']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="tambah-form-group">
          <label for="umur_aset">Umur Aset (tahun)</label>
          <input type="number" name="umur_aset" value="<?php echo htmlspecialchars($data['umur_aset']) ?>" required>
        </div>

        <div class="tambah-form-group">
          <label for="harga-beli">Harga Beli</label>
          <input type="number" name="harga_beli" value="<?php echo htmlspecialchars($data['harga_beli']) ?>" required>
        </div>

        <div class="tambah-form-group">
          <label>Kondisi</label>
          <div class="tambah-radio-group">
            <label><input type="radio" name="kondisi" value="Baik" <?php echo $data['kondisi']=='Baik'?'checked':'' ?>> Baik</label>
            <label><input type="radio" name="kondisi" value="Rusak Ringan" <?php echo $data['kondisi']=='Rusak Ringan'?'checked':'' ?>> Rusak Ringan</label>
            <label><input type="radio" name="kondisi" value="Rusak Berat" <?php echo $data['kondisi']=='Rusak Berat'?'checked':'' ?>> Rusak Berat</label>
          </div>
        </div>
        
        <div class="tambah-form-group">
          <label for="sumber-dana">Sumber Dana</label>
          <input type="text" name="sumber_dana" value="<?php echo htmlspecialchars($data['sumber_dana']) ?>" required>
        </div>

        <div class="tambah-form-group">
          <label for="status">Status</label>
          <select name="status" required>
            <option value="beroperasi" <?php echo $data['status']=='beroperasi'?'selected':'' ?>>Beroperasi</option>
            <option value="tidak-beroperasi" <?php echo $data['status']=='tidak-beroperasi'?'selected':'' ?>>Tidak Beroperasi</option>
            <option value="perbaikan" <?php echo $data['status']=='perbaikan'?'selected':'' ?>>Dalam Perbaikan</option>
            <option value="pensiun-dibuang" <?php echo $data['status']=='pensiun-dibuang'?'selected':'' ?>>Pensiun/Dibuang</option>
          </select>
        </div>

        <div class="tambah-form-group">
          <label for="id_pj">Penanggung Jawab</label>
          <select name="id_pj" required>
            <?php while ($pj = mysqli_fetch_assoc($pj_result)): ?>
              <option value="<?php echo $pj['id_pj'] ?>" <?php echo $data['id_pj'] == $pj['id_pj'] ? 'selected' : '' ?>>
                <?php echo htmlspecialchars($pj['nama_pj']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="tambah-button-group">
          <button type="submit" class="tambah-btn-submit">Update</button>
          <a href="dashboard.php" class="tambah-btn-cancel">Batal</a>
        </div>

      </form>
    </div>
</body>
</html>