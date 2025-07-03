<?php
require 'vendor/autoload.php';
include 'db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'ID');
$sheet->setCellValue('C1', 'Nama Aset');
$sheet->setCellValue('D1', 'Kategori');
$sheet->setCellValue('E1', 'Tanggal Beli');
$sheet->setCellValue('F1', 'Umur Aset');
$sheet->setCellValue('G1', 'Harga Beli');
$sheet->setCellValue('H1', 'Status');

// Ambil data dari database
$result = mysqli_query($conn, "SELECT aset.*, kategori.nama_kategori
        FROM aset 
        LEFT JOIN kategori ON aset.id_kategori = kategori.id_kategori");
$rowNum = 2;
$no = 1;
while($row = mysqli_fetch_assoc($result)) {
    $umur = number_format($row['umur_aset']). ' Tahun';

    $sheet->setCellValue('A'.$rowNum, $no++);
    $sheet->setCellValue('B'.$rowNum, $row['id_aset']);
    $sheet->setCellValue('C'.$rowNum, $row['nama_aset']);
    $sheet->setCellValue('D'.$rowNum, $row['nama_kategori']);
    $sheet->setCellValue('E'.$rowNum, $row['tanggal_beli']);
    $sheet->setCellValue('F'.$rowNum, $umur);
    $sheet->setCellValue('G'.$rowNum, number_format($row['harga_beli']));
    $sheet->setCellValue('H'.$rowNum, $row['status']);
    $rowNum++;
}

// Output ke browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan_aset.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>