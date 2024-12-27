<?php
include_once '../db.php';

$database = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // First, check if the permission is being used
    $checkQuery = "SELECT COUNT(*) as count FROM user_permissions WHERE permission_id = :id 
                   UNION ALL 
                   SELECT COUNT(*) as count FROM role_permissions WHERE permission_id = :id";
    $checkParams = ['id' => $id];
    $results = $database->fetchAll($checkQuery, $checkParams);

    $userCount = $results[0]['count'];
    $roleCount = $results[1]['count'];

    if ($userCount > 0 || $roleCount > 0) {
        echo json_encode(['success' => false, 'message' => 'Bu yetki kullanımda olduğu için silinemez.']);
    } else {
        $query = "DELETE FROM permissions WHERE id = :id";
        $params = ['id' => $id];

        if ($database->delete($query, $params)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Yetki silinirken bir hata oluştu.']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu.']);
}