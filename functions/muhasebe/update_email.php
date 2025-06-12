<?php
include_once '../db.php';
$database = new Database();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $email = $_POST['email'] ?? null;

    if ($id && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sql = "UPDATE vadesi_gecmis_borc SET email = :email WHERE id = :id";
        $result = $database->update($sql, [
            'email' => $email,
            'id' => $id
        ]);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Veritabanı güncellenemedi.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Geçersiz veri.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek.']);
}
