<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "inventaris";

// Aktifkan laporan error saat development (hapus di production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    // Jangan tampilkan detail error di production
    die("Koneksi database gagal.");
    exit;
}
?>