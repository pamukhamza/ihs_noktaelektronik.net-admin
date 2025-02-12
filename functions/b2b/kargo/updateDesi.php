<?php
include("../../db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    try {
        $database = new Database();
        $id = $_POST["id"];
        $desiUst = $_POST["desi_ust"];
        $desiAlt = $_POST["desi_alt"];
        $fiyat = $_POST["fiyat"];

        // Güncelleme sorgusu
        $query = "UPDATE b2b_kargo_desi SET desi_ust = :desiUst, desi_alt = :desiAlt, fiyat = :fiyat WHERE id = :id";
        $params = array(
            'desiUst' => $desiUst,
            'desiAlt' => $desiAlt,
            'fiyat' => $fiyat,
            'id' => $id
        );

        $success = $database->update($query, $params);

        if ($success) {
            echo json_encode(["status" => "success"]);
        } else {
            throw new Exception("Güncelleme başarısız.");
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Geçersiz istek."]);
}
?>
