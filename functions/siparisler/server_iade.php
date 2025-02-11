<?php
include '../db.php';

$database = new Database();

$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// Get column index and sort direction
$columns = $_POST['columns'];
$orderColumn = $columns[$_POST['order'][0]['column']]['data'];
$orderDirection = $_POST['order'][0]['dir'];

// Base query
$sql = "SELECT i.*, u.firmaUnvani, su.sip_id, su.urun_id, su.iade, s.siparis_no, nu.UrunAdiTR, id.durum 
        FROM b2b_iadeler AS i
        LEFT JOIN uyeler AS u ON u.id = i.uye_id
        LEFT JOIN b2b_siparis_urunler AS su ON su.id = i.sip_urun_id
        LEFT JOIN b2b_siparisler AS s ON s.id = su.sip_id
        LEFT JOIN nokta_urunler AS nu ON nu.id = su.urun_id
        LEFT JOIN b2b_iade_durum AS id ON id.iade_id = i.durum
        WHERE 1=1";

// Add search condition if a search term is provided
$params = [];
if ($searchValue != '') {
    $sql .= " AND (s.siparis_no LIKE :searchValue OR i.iade_nedeni LIKE :searchValue)";
    $params['searchValue'] = '%' . $searchValue . '%';
}

// Add ORDER BY clause based on the column and direction
$sql .= " ORDER BY $orderColumn $orderDirection LIMIT :start, :length";
$params['start'] = $start;
$params['length'] = $length;

// Execute the query
$results = $database->fetchAll($sql, $params);

$data = array();
foreach ($results as $row) {
    $data[] = $row;
}

// Calculate total records considering the search term
$totalQuery = "SELECT COUNT(*) AS total FROM b2b_iadeler AS i
               LEFT JOIN uyeler AS u ON u.id = i.uye_id
               LEFT JOIN b2b_siparis_urunler AS su ON su.id = i.sip_urun_id
               LEFT JOIN b2b_siparisler AS s ON s.id = su.sip_id
               LEFT JOIN nokta_urunler AS nu ON nu.id = su.urun_id
               LEFT JOIN b2b_iade_durum AS id ON id.iade_id = i.durum
               WHERE 1=1";
$totalParams = [];
if ($searchValue != '') {
    $totalQuery .= " AND (s.siparis_no LIKE :searchValue OR i.iade_nedeni LIKE :searchValue)";
    $totalParams['searchValue'] = '%' . $searchValue . '%';
}

// Execute the total query
$totalResult = $database->fetch($totalQuery, $totalParams);
$total = $totalResult['total'];

$response = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => $total,
    "recordsFiltered" => $total,
    "data" => $data
);

echo json_encode($response);
?>