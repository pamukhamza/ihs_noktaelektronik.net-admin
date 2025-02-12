<?php
include("../../db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $database = new Database();
    $id = $_POST["id"];

    // Desi bilgisini sil
    $query = "DELETE FROM b2b_kargo_desi WHERE id = :id";
    $success = $database->delete($query, array('id' => $id));

    // Başarılı bir şekilde silindiğini kontrol et
    if ($success) {echo 'Success';
    } else {echo 'Error';}
} else {echo 'Error';}
?>
