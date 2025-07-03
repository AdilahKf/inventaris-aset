<?php
include 'db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    echo "Akses ditolak!";
    exit();
}

if (isset($_POST['hapus']) && isset($_POST['id_aset'])) {
    $ids = $_POST['id_aset']; // array of IDs

    // Amankan tiap ID
    $escaped_ids = array_map(function($id) use ($conn) {
        return "'" . mysqli_real_escape_string($conn, $id) . "'";
    }, $ids);

    $id_list = implode(',', $escaped_ids); // 'ATK001','ELK002',...

    $sql = "DELETE FROM aset WHERE id_aset IN ($id_list)";
    if ($conn->query($sql)) {
        echo "<script>alert('Data berhasil dihapus!');window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!');window.location='dashboard.php';</script>";
    }
} else {
    echo "<script>alert('Tidak ada data yang dipilih!');window.location='dashboard.php';</script>";
}

$conn->close();
?>
