<?php
require_once "../db.php";

$database = new Database();
$ilID = $_POST["il_id"];
$uye_ilce = $_POST["ilce"] ?? null;

$ilceler = $database->fetchAll("SELECT ilce_id, ilce_adi FROM ilceler WHERE il_id = :ilID", ['ilID' => $ilID] );

// Eğer ilçe seçili değilse "İlçe *" göster
if (empty($uye_ilce)) {
    echo '<option value="">İlçe *</option>';
} else {
    // Seçili ilçenin adını getir
    $uye_ilce_adi = $database->fetchColumn( "SELECT ilce_adi FROM ilceler WHERE ilce_id = :uye_ilce", ['uye_ilce' => $uye_ilce] );
    echo '<option value="' . $uye_ilce . '">' . $uye_ilce_adi . '</option>';
}
// İlçeleri listele
foreach ($ilceler as $row) {
    echo '<option value="' . $row["ilce_id"] . '">' . $row["ilce_adi"] . '</option>';
}
?>
