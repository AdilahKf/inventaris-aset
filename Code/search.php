<?php
include 'db.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$page = $_GET['page'] ?? 'dashboard';

$sql = "SELECT aset.*, kategori.nama_kategori, penanggung_jawab.nama_pj 
        FROM aset 
        LEFT JOIN kategori ON aset.id_kategori = kategori.id_kategori 
        LEFT JOIN penanggung_jawab ON aset.id_pj = penanggung_jawab.id_pj";

$conditions = [];

if ($search !== '') {
    $conditions[] = "aset.id_aset LIKE '%$search%'";
    $conditions[] = "aset.nama_aset LIKE '%$search%'";
    $conditions[] = "kategori.nama_kategori LIKE '%$search%'";
    $conditions[] = "aset.kondisi LIKE '%$search%'";
    $conditions[] = "aset.status LIKE '%$search%'";
    $conditions[] = "aset.sumber_dana LIKE '%$search%'";
    $conditions[] = "penanggung_jawab.nama_pj LIKE '%$search%'";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" OR ", $conditions);
}


$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    if ($page === 'dashboard') {
        echo "<tr>";
        echo '<td><input type="checkbox" name="pilih[]" value="' . $row['id_aset'] . '" class="dashboard-checkbox"></td>';
        echo "<td>".htmlspecialchars($row['id_aset'])."</td>";
        echo "<td>".htmlspecialchars($row['nama_aset'])."</td>";
        echo "<td>".htmlspecialchars($row['nama_kategori'])."</td>";
        echo "<td>".htmlspecialchars($row['kondisi'])."</td>";
        echo "<td>".htmlspecialchars($row['status'])."</td>";
        echo "<td>".htmlspecialchars($row['sumber_dana'])."</td>";
        echo "<td>".htmlspecialchars($row['tanggal_beli'])."</td>";
        echo "<td>".htmlspecialchars(number_format($row['umur_aset'], 0, ',', '.'))."</td>";
        echo "<td>".htmlspecialchars(number_format($row['harga_beli'], 0, ',', '.'))."</td>";
        echo "<td>".htmlspecialchars($row['nama_pj'] ?? '-')."</td>";
        echo "</tr>";
    } else if ($page === 'datamaster') {
        $harga = $row['harga_beli'];
        $umur = $row['umur_aset'];
        $penyusutan = $umur > 0 ? $harga / $umur : 0;
        $tahun_beli = date('Y', strtotime($row['tanggal_beli']));
        $tahun_sekarang = date('Y');
        $pakai = max(0, $tahun_sekarang - $tahun_beli);
        $nilai_buku = max(0, $harga - ($penyusutan * $pakai));

        echo "<tr>";
        echo "<td>".htmlspecialchars($row['id_aset'])."</td>";
        echo "<td>".htmlspecialchars($row['nama_aset'])."</td>";
        echo "<td>".htmlspecialchars($row['nama_kategori'] ?? '-')."</td>";
        echo "<td>".htmlspecialchars($row['tanggal_beli'])."</td>";
        echo "<td>".number_format($harga, 0, ',', '.')."</td>";
        echo "<td>".htmlspecialchars($umur)." Tahun</td>";
        echo "<td>".number_format($penyusutan, 0, ',', '.')."</td>";
        echo "<td>".number_format($nilai_buku, 0, ',', '.')."</td>";
        echo "<td>".htmlspecialchars($row['sumber_dana'])."</td>";
        echo "<td>".htmlspecialchars($row['nama_pj'] ?? '-')."</td>";
        echo "</tr>";
    }
}
?>
