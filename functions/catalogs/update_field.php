<?php
require '../db.php';

$database = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gelen JSON verisini al ve ayrıştır
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'], $data['field'], $data['value'])) {
        $id = intval($data['id']);
        $field = $data['field'];
        $value = intval($data['value']);

        // Güvenlik: Sadece belirli alanların güncellenmesine izin ver
        $allowedFields = ['web_net', 'web_comtr', 'web_cn'];
        if (!in_array($field, $allowedFields)) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz alan']);
            exit;
        }

        // Veritabanı sorgusunu çalıştır
        $query = "UPDATE catalogs SET `$field` = :value WHERE id = :id";
        $params = [
            'value' => $value,
            'id' => $id
        ];

        $result = $database->update($query, $params);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Güncelleme başarılı']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Güncelleme başarısız']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Eksik veya geçersiz veri']);
    }
    exit();
}
?>
