<?php
require('library/fpdf.php');
include 'db.php';

$pdf = new FPDF('L','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Times','B',16);
$pdf->Cell(0,10,'Laporan Data Aset',0,1,'C'); // Judul di tengah

$pdf->SetFont('Times','B',12);
// Header Tabel
$pdf->Cell(10,7,'No',1,0,'C');
$pdf->Cell(25,7,'ID',1,0,'C');
$pdf->Cell(50,7,'Nama Aset',1,0,'C');
$pdf->Cell(35,7,'Kategori',1,0,'C');
$pdf->Cell(35,7,'Tanggal Beli',1,0,'C');
$pdf->Cell(30,7,'Umur Aset',1,0,'C');
$pdf->Cell(35,7,'Harga Beli',1,0,'C');
$pdf->Cell(35,7,'Status',1,1,'C');

$pdf->SetFont('Times','',12);
$no = 1;
$data = mysqli_query($conn, "SELECT aset.*, kategori.nama_kategori 
        FROM aset 
        LEFT JOIN kategori ON aset.id_kategori = kategori.id_kategori");
while($d = mysqli_fetch_array($data)){
    $umur = number_format($d['umur_aset']). ' Tahun';

    $pdf->Cell(10,6, $no++,1,0,'C');
    $pdf->Cell(25,6, $d['id_aset'],1,0);
    $pdf->Cell(50,6, $d['nama_aset'],1,0);
    $pdf->Cell(35,6, $d['nama_kategori'],1,0);
    $pdf->Cell(35,6, $d['tanggal_beli'],1,0);
    $pdf->Cell(30,6, $umur,1,0,'C');
    $pdf->Cell(35,6, number_format($d['harga_beli'], 0, ',', '.'),1,0);
    $pdf->Cell(35,6, $d['status'],1,1);
}
$pdf->Output('I', 'laporan_aset.pdf');
?>