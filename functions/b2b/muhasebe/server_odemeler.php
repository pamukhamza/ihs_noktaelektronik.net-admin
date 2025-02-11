<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../db.php';

// Set header to return JSON
header('Content-Type: application/json');

try {
    $database = new Database();

    // Basic parameters
    $start = isset($_POST['start']) ? (int) $_POST['start'] : 0;
    $length = isset($_POST['length']) ? (int) $_POST['length'] : 0;
    $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

    $columns = $_POST['columns'];
    $orderColumnIndex = isset($_POST['order'][0]['column']) ? (int) $_POST['order'][0]['column'] : 0;
    $orderColumn = isset($columns[$orderColumnIndex]['data']) ? $columns[$orderColumnIndex]['data'] : 's.id';
    $orderDirection = isset($_POST['order'][0]['dir']) && in_array(strtoupper($_POST['order'][0]['dir']), ['ASC', 'DESC']) ? strtoupper($_POST['order'][0]['dir']) : 'ASC';

    // Construct base SQL query
    $sql = "SELECT s.*, u.firmaUnvani, u.muhasebe_kodu, d.dekont AS dekont
            FROM b2b_sanal_pos_odemeler AS s
            LEFT JOIN uyeler AS u ON u.id = s.uye_id
            LEFT JOIN b2b_dekontlar AS d ON d.pos_odeme_id = s.id 
            WHERE 1";  // Always true, for initial query

    if (!empty($searchValue)) {
        $sql .= " AND (s.islem LIKE "%$searchValue%" OR u.firmaUnvani LIKE "%$searchValue%" OR u.muhasebe_kodu LIKE "%$searchValue%")";
    }

    $minTutar = isset($_POST['minTutar']) ? floatval($_POST['minTutar']) : null;
    $maxTutar = isset($_POST['maxTutar']) ? floatval($_POST['maxTutar']) : null;
    if ($minTutar !== null) {
        $sql .= " AND s.tutar >= $minTutar";
    }
    if ($maxTutar !== null) {
        $sql .= " AND s.tutar <= $maxTutar";
    }

    $minTarih = isset($_POST['minTarih']) ? $_POST['minTarih'] : '';
    $maxTarih = isset($_POST['maxTarih']) ? $_POST['maxTarih'] : '';
    if (!empty($minTarih) && !empty($maxTarih)) {
        $sql .= " AND s.tarih BETWEEN $minTarih AND $maxTarih";
    }

    $basarili = isset($_POST['basarili']) ? $_POST['basarili'] : '';
    if ($basarili !== '') {
        $sql .= " AND s.basarili = $basarili";
    }

    // Final SQL query for fetching the data
    $sql .= " ORDER BY $orderColumn $orderDirection LIMIT $start, $length";


    // Debug log
    error_log("SQL Query: " . $sql);

    // Execute main query
    $data = $database->fetchAll($sql);
    // Get total count
    $totalQuery = "SELECT COUNT(*) AS total 
                   FROM b2b_sanal_pos_odemeler AS s
                   LEFT JOIN uyeler AS u ON u.id = s.uye_id
                   LEFT JOIN b2b_dekontlar AS d ON d.pos_odeme_id = s.id WHERE 1";

    $totalParams = [];
    if (!empty($searchValue)) {
        $totalQuery .= " AND (s.islem LIKE :searchValue OR u.firmaUnvani LIKE :searchValue OR u.muhasebe_kodu LIKE :searchValue)";
        $totalParams['searchValue'] = "%$searchValue%";
    }

    $totalResult = $database->fetch($totalQuery, $totalParams);
    $total = isset($totalResult['total']) ? intval($totalResult['total']) : 0;

    // Format data
    $data = array_map(function($row) {
        $row['DT_RowId'] = $row['id'];
        return $row;
    }, $data ?: []);

    // Return response
    echo json_encode([
        "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
        "recordsTotal" => $total,
        "recordsFiltered" => $total,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Log error
    error_log("Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());

    // Return error response
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
