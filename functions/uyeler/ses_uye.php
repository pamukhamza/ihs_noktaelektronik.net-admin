<?php
include("../db.php"); // Database sınıfını içe aktar

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["userId"])) {
    $userId = $_POST["userId"];

    // Database sınıfını kullanarak kullanıcıyı sorgula
    $db = new Database();
    $sql = "SELECT * FROM uyeler WHERE id = :userId";
    $param = ["userId" => $userId] ;
    $user = $db->fetch($sql, $param);

    if ($user) {
        session_name("user_session");
        session_start();
        $_SESSION['id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['firma'] = $user['firmaUnvani'];
        $_SESSION['BLKODU'] = $user['BLKODU'];
        $_SESSION['ad'] = $user['ad'];
        $_SESSION['soyad'] = $user['soyad'];

        session_regenerate_id(true);

        echo json_encode(array("status" => "success", "message" => "Session oluşturuldu."));
    } else {
        echo json_encode(array("status" => "error", "message" => "Kullanıcı bulunamadı."));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Geçersiz istek."));
}
?>