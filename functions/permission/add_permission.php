<?php
include_once '../db.php';

$database = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $parent_id = $_POST['parent_id'];

    $query = "INSERT INTO permissions (name, description, parent_id) VALUES (:name, :description, :parent_id)";
    $params = [
        'name' => $name,
        'description' => $description,
        'parent_id' => $parent_id
    ];

    if ($database->insert($query, $params)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}