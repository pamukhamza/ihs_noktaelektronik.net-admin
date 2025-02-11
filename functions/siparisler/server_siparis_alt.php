<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';

// Set header to return JSON
header('Content-Type: application/json');

// Log all incoming request data
error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));
error_log("RAW input: " . file_get_contents('php://input'));

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    }

    // Check if id exists in POST or GET
    $id = null;
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } elseif (isset($_GET['id'])) {
        $id = $_GET['id'];
    }

    if ($id === null) {
        throw new Exception("Missing required parameter: id");
    }

    if (!is_numeric($id)) {
        throw new Exception("Invalid id parameter: " . $id);
    }

    $database = new Database();
    $parentRowId = intval($id);

    // Debug log
    error_log("Processing child table request for parent ID: " . $parentRowId);

    // Query to fetch data for the child table
    $query = "SELECT 
        b2b_siparis_urunler.*,
        nokta_urunler.UrunKodu,
        nokta_urunler.UrunAdiTR,
        nokta_urunler.seo_link,
        nokta_urunler.DSF4,
        COALESCE(MIN(nokta_urunler_resimler.KResim), 'default.jpg') AS foto
    FROM b2b_siparis_urunler
    LEFT JOIN nokta_urunler ON b2b_siparis_urunler.BLKODU = nokta_urunler.BLKODU
    LEFT JOIN nokta_urunler_resimler ON nokta_urunler.id = nokta_urunler_resimler.UrunID
    WHERE b2b_siparis_urunler.sip_id = :parentId
    GROUP BY b2b_siparis_urunler.id";

    $params = ['parentId' => $parentRowId];

    // Debug log
    error_log("Child table query: " . $query);
    error_log("Parameters: " . print_r($params, true));

    $results = $database->fetchAll($query, $params);

    // Debug log
    error_log("Query results count: " . count($results));
    error_log("Query results: " . print_r($results, true));

    // Format data for DataTables
    $data = array_map(function($row) {
        // Ensure numeric values are properly formatted
        $row['birim_fiyat'] = floatval($row['birim_fiyat']);
        $row['adet'] = intval($row['adet']);
        $row['dolar_satis'] = floatval($row['dolar_satis']);
        return $row;
    }, $results);

    $response = [
        'data' => $data,
        'success' => true
    ];

    error_log("Sending response: " . print_r($response, true));
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Log the error
    error_log("Child Table Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Return error response
    http_response_code(400);
    $errorResponse = [
        'success' => false,
        'error' => $e->getMessage(),
        'message' => 'An error occurred while fetching order details: ' . $e->getMessage(),
        'data' => []
    ];
    error_log("Sending error response: " . print_r($errorResponse, true));
    echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE);
}
?>