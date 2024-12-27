<?php
include_once '../db.php';

$database = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    $query = "INSERT INTO permissions (name, description) VALUES (:name, :description)";
    $params = [
        'name' => $name,
        'description' => $description
    ];

    if ($database->insert($query, $params)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}