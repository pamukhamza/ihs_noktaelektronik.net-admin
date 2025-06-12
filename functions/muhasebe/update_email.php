<?php
include_once '../../functions/db.php';

// Hata raporlamayı aktif et
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS ayarları
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// OPTIONS isteğini yanıtla
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Sadece POST istekleri kabul edilir.');
    }

    // POST verilerini al
    $postData = file_get_contents('php://input');
    $data = json_decode($postData, true) ?? $_POST;

    $database = new Database();
    
    $id = $data['id'] ?? null;
    $email = $data['email'] ?? null;
    
    if (!$id) {
        throw new Exception('ID parametresi eksik.');
    }
    
    if (!$email) {
        throw new Exception('Email parametresi eksik.');
    }
    
    // Email formatını kontrol et
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Geçersiz email formatı.');
    }
    
    // Önce kaydın var olduğunu kontrol et
    $checkSql = "SELECT id FROM vadesi_gecmis_borc WHERE id = :id";
    $checkParams = ['id' => $id];
    $exists = $database->fetch($checkSql, $checkParams);
    
    if (!$exists) {
        throw new Exception('Belirtilen ID\'ye sahip kayıt bulunamadı.');
    }
    
    // Email sütununun varlığını kontrol et
    $columnCheckSql = "SHOW COLUMNS FROM vadesi_gecmis_borc LIKE 'email'";
    $columnExists = $database->fetch($columnCheckSql);
    
    if (!$columnExists) {
        // Email sütunu yoksa ekle
        $alterSql = "ALTER TABLE vadesi_gecmis_borc ADD COLUMN email VARCHAR(255) DEFAULT NULL";
        $database->query($alterSql);
    }
    
    // Güncelleme işlemini yap
    $sql = "UPDATE vadesi_gecmis_borc SET email = :email WHERE id = :id";
    $params = [
        'email' => $email,
        'id' => $id
    ];
    
    $result = $database->update($sql, $params);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Email başarıyla güncellendi.'
        ]);
    } else {
        throw new Exception('Güncelleme işlemi başarısız oldu.');
    }
    
} catch (Exception $e) {
    error_log('Email güncelleme hatası: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 