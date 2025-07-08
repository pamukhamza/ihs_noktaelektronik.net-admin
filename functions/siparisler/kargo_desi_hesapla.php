<?php
include "../db.php";
$database = new Database();

$toplamDesi = $_POST['toplamDesi'];

$kargoIds = [2, 3, 1]; // Almak istediğiniz kargo id'leri
$kargoUcretleri = [];

foreach ($kargoIds as $idsi) {
    $fiyatRow = $database->fetch("SELECT * FROM b2b_kargo_desi WHERE kargo_id = :id AND :desi BETWEEN desi_alt AND desi_ust", ['id' => $idsi, 'desi' => $toplamDesi]);
    $uygunKargoUcreti = $fiyatRow ? $fiyatRow['fiyat'] : 0.00;
    array_push($kargoUcretleri, $uygunKargoUcreti);
}

echo json_encode($kargoUcretleri);
?>