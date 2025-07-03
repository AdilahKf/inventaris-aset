<?php
include 'db.php';

$id_kategori = $_GET['id_kategori'];

$query = "SELECT id_aset FROM aset WHERE id_aset LIKE '{$id_kategori}%' ORDER BY id_aset DESC LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $last_id = mysqli_fetch_assoc($result)['id_aset'];
    $last_num = intval(substr($last_id, strlen($id_kategori)));
    $new_num = str_pad($last_num + 1, 4, "0", STR_PAD_LEFT);
} else {
    $new_num = "0001";
}

echo $id_kategori . $new_num;
?>