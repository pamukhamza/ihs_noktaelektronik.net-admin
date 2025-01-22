<?php
require 'vendor/autoload.php'; // PhpSpreadsheet'i dahil edin

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Veritabanı bağlantısı
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'nktdnm'; // Veritabanı adınızı yazın

$mysqli = new mysqli($host, $user, $password, $database);

if ($mysqli->connect_error) {
    die("Veritabanı bağlantı hatası: " . $mysqli->connect_error);
}

// Verileri çekme sorgusu
$query = "SELECT id, old_id FROM nokta_urunler_net";
$result = $mysqli->query($query);

if (!$result) {
    die("Sorgu hatası: " . $mysqli->error);
}

// Spreadsheet nesnesi oluşturma
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Başlıkları ekleme
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'OLD ID');

// Verileri Excel'e aktarma
$rowNumber = 2; // Veriler 2. satırdan başlıyor
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $row['id']);
    $sheet->setCellValue('B' . $rowNumber, $row['old_id']);
    $rowNumber++;
}

// Çıktı ayarları
$filename = 'nokta_urunler.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"$filename\"");
header('Cache-Control: max-age=0');

// Excel dosyasını çıktı olarak gönderme
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Belleği temizle
exit;
?>
