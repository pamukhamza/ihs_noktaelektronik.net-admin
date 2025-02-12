<?php
include("../../db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["kargoID"])) {
    try {
        $database = new Database();
        $kargoID = $_POST["kargoID"];

        // Yeni satırı ekle
        $insertQuery = "INSERT INTO b2b_kargo_desi (kargo_id, desi_alt, desi_ust, fiyat) VALUES (:kargo_id, '0', '0', '0')";
        $insertSuccess = $database->insert($insertQuery, array('kargo_id' => $kargoID));

        if ($insertSuccess) {
            // Son eklenen satırın ID'sini al
            $lastInsertId = $database->lastInsertId();

            // Yeni eklenen satırın bilgilerini al
            $desiData = $database->fetch("SELECT * FROM b2b_kargo_desi WHERE id = :id", array('id' => $lastInsertId));

            if ($desiData) {
                // JSON olarak encode edilmiş yeni satır verisini gönder
                header('Content-Type: application/json');
                echo json_encode($desiData);
                exit;
            } else {
                throw new Exception("Eklenen veri bulunamadı.");
            }
        } else {
            throw new Exception("Veri ekleme başarısız.");
        }
    } catch (Exception $e) {
        echo json_encode(array("error" => $e->getMessage()));
        exit;
    }
} else {
    echo json_encode(array("error" => "Geçersiz istek."));
    exit;
}
?>
