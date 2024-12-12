<?php
header('Content-Type: application/json');
include 'db.php';
$response = ['success' => false, 'message' => ''];
if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $email = $_POST['email'];
    $database = new Database();
    // E-posta adresinin var olup olmadığını kontrol et
    $checkQuery = "SELECT COUNT(*) FROM newsletter WHERE email = :email";
    $checkParams = ['email' => $email];
    $count = $database->fetchColumn($checkQuery, $checkParams); // fetchColumn kullanımı
    if ($count > 0) {
        $response['message'] = 'Bu e-posta adresi zaten kayıtlı.';
    } else {
        // E-posta adresini ekle
        $query = "INSERT INTO newsletter (email) VALUES (:email)";
        $params = ['email' => $email];

        try {
            $database->insert($query, $params);
            $response['success'] = true;
            $response['message'] = 'Başarıyla abone oldunuz!';
        } catch (Exception $e) {
            $response['message'] = 'Bir hata oluştu. Lütfen tekrar deneyin!.';
        }
    }
} else {
    $response['message'] = 'Geçerli bir e-posta adresi giriniz.';
}
echo json_encode($response);
?>