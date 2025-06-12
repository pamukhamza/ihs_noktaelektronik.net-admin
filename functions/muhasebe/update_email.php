<?php
include_once '../../../functions/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    
    $id = $_POST['id'] ?? null;
    $email = $_POST['email'] ?? null;
    
    if ($id && $email) {
        // Email formatını kontrol et
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $sql = "UPDATE vadesi_gecmis_borc SET email = :email WHERE id = :id";
            $params = [
                'email' => $email,
                'id' => $id
            ];
            
            $result = $database->update($sql, $params);
            
            if ($result) {
                echo json_encode(['success' => true]);
                exit;
            }
        }
    }
}

echo json_encode(['success' => false]); 