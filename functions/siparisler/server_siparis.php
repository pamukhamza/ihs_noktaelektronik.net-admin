<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';

// Set header to return JSON
header('Content-Type: application/json');

try {
    $database = new Database();
    
    // Basic parameters
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $sDurum = isset($_POST['sDurum']) ? intval($_POST['sDurum']) : 0;
    
    // Debug log
    error_log("Request parameters - start: $start, length: $length, sDurum: $sDurum");
    
    // Using named parameters for the queries
    if ($sDurum == 0) {
        $sql = "SELECT SQL_CALC_FOUND_ROWS 
            id, siparis_no, uye_ad, uye_soyad, uye_firmaadi, 
            uye_email, tarih, durum, odeme_sekli, kargo_firmasi, toplam
            FROM b2b_siparisler 
            ORDER BY id DESC 
            LIMIT :start, :length";
        $params = [
            'start' => $start,
            'length' => $length
        ];
    } else {
        $sql = "SELECT SQL_CALC_FOUND_ROWS 
            id, siparis_no, uye_ad, uye_soyad, uye_firmaadi, 
            uye_email, tarih, durum, odeme_sekli, kargo_firmasi, toplam
            FROM b2b_siparisler 
            WHERE durum = :durum 
            ORDER BY id DESC 
            LIMIT :start, :length";
        $params = [
            'durum' => $sDurum,
            'start' => $start,
            'length' => $length
        ];
    }
    
    // Debug log
    error_log("SQL Query: " . $sql);
    error_log("Parameters: " . print_r($params, true));
    
    // Execute main query
    $results = $database->fetchAll($sql, $params);
    
    // Get total count
    $totalResult = $database->fetch("SELECT FOUND_ROWS() as total");
    $total = isset($totalResult['total']) ? intval($totalResult['total']) : 0;
    
    // Format data
    $data = array_map(function($row) {
        $row['DT_RowId'] = $row['id'];
        $row['toplam'] = number_format((float)$row['toplam'], 2, '.', '');
        return $row;
    }, $results ?: []);
    
    // Response
    echo json_encode([
        "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
        "recordsTotal" => $total,
        "recordsFiltered" => $total,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("Error in server_siparis.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "An error occurred while processing your request"
    ]);
}
?>