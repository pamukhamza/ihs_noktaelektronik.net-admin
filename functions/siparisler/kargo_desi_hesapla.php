<?php
include "../db.php";
$database = new Database();

$toplamDesi = $_POST['toplamDesi'];

$kargoIds = [2, 3, 1]; // Almak istediğiniz kargo id'leri
$kargoUcretleri = [];

foreach ($kargoIds as $idsi) {
    $stmt = $db->prepare("SELECT * FROM kargo_desi WHERE kargo_id = :id AND :desi BETWEEN desi_alt AND desi_ust");
    $stmt->bindParam(':id', $idsi, PDO::PARAM_INT);
    $stmt->bindParam(':desi', $toplamDesi, PDO::PARAM_INT);
    $stmt->execute();
    $fiyatRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $uygunKargoUcreti = $fiyatRow ? $fiyatRow['fiyat'] : 0.00;
    array_push($kargoUcretleri, $uygunKargoUcreti);
}

echo json_encode($kargoUcretleri);
?>
