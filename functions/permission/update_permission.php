<?php
include_once '../db.php';

$database = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];

    $query = "UPDATE permissions SET name = :name, description = :description WHERE id = :id";
    $params = [
        'id' => $id,
        'name' => $name,
        'description' => $description
    ];

    if ($database->update($query, $params)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Yetki güncellenirken bir hata oluştu.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu.']);
}