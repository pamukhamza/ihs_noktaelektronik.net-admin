<?php
require_once "../db.php";

$database = new Database();
$ilceID = $_POST["ilce_id"];
$mahalleID = $_POST["mahalle"] ?? null;

// Seçili ilçeye ait mahalleleri çek
$mahalleler = $database->fetchAll( "SELECT mahalle_id, mahalle_adi FROM mahalleler WHERE ilce_id = :ilceID", ['ilceID' => $ilceID] );

// Seçili mahalle boşsa "Seçiniz.." göster
if (empty($mahalleID)) {
    echo '<option value="">Seçiniz..</option>';
} else {
    // Seçili mahalle adını getir
    $mahalle_adi = $database->fetchColumn("SELECT mahalle_adi FROM mahalleler WHERE mahalle_id = :mahalleID",['mahalleID' => $mahalleID]);
    echo '<option value="' . $mahalleID . '">' . $mahalle_adi . '</option>';
}
// Mahalleleri listele
foreach ($mahalleler as $row) {
    echo '<option value="' . $row["mahalle_id"] . '">' . $row["mahalle_adi"] . '</option>';
}
?>
