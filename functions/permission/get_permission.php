<?php
include_once '../db.php';

$database = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'];

    $query = "SELECT * FROM permissions WHERE id = :id";
    $params = ['id' => $id];

    $permission = $database->fetch($query, $params);

    if ($permission) {
        echo json_encode(['success' => true, 'permission' => $permission]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Yetki bulunamadı.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu.']);
}