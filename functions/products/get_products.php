<?php
include_once '../db.php';

$database = new Database();

if (isset($_POST['allData']) && $_POST['allData'] == true) {
    $query = "SELECT p.id, p.UrunKodu, p.UrunAdiTR, m.id AS mid, m.title, c.KategoriAdiTR AS category_name, p.Vitrin, p.YeniUrun, p.aktif
    FROM nokta_urunler p
    LEFT JOIN nokta_kategoriler c ON p.KategoriID = c.id
    LEFT JOIN nokta_urun_markalar AS m ON m.id = p.MarkaID";
    $data = $database->fetchAll($query);
    ob_clean(); // Clear previous output buffers
    echo json_encode($data);
    exit;
}

// Get parameters from DataTables
$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);
$searchValue = $_POST['search']['value'];

// Build the query
$query = "
    SELECT p.id, p.UrunKodu, p.UrunAdiTR, m.id AS mid, m.title, c.KategoriAdiTR AS category_name, p.Vitrin, p.YeniUrun, p.aktif
    FROM nokta_urunler p
    LEFT JOIN nokta_kategoriler c ON p.KategoriID = c.id
    LEFT JOIN nokta_urun_markalar AS m ON m.id = p.MarkaID
";

// Add search functionality
$params = [];
if (!empty($searchValue)) {
    $query .= " WHERE p.UrunAdiTR LIKE :search1 
                OR p.UrunKodu LIKE :search2 
                OR m.title LIKE :search3 
                OR c.KategoriAdiTR LIKE :search4";
    $params['search1'] = '%' . $searchValue . '%';
    $params['search2'] = '%' . $searchValue . '%';
    $params['search3'] = '%' . $searchValue . '%';
    $params['search4'] = '%' . $searchValue . '%';
}

// Get total records without filtering
$totalRecordsQuery = "SELECT COUNT(*) as total FROM nokta_urunler";
$totalRecordsResult = $database->fetchAll($totalRecordsQuery);
$totalRecords = $totalRecordsResult[0]['total'];

// Get filtered records count
$filteredRecordsQuery = $query;
$filteredRecordsResult = $database->fetchAll($filteredRecordsQuery, $params);
$totalFilteredRecords = count($filteredRecordsResult);

// Add pagination
$query .= " LIMIT " . intval($start) . ", " . intval($length);

// Debugging: Output the final query and parameters
error_log('Final query: ' . $query);
error_log('Parameters: ' . print_r($params, true));

$results = $database->fetchAll($query, $params);

// Debugging: Output memory usage
error_log('Memory usage before processing results: ' . memory_get_usage());

// Prepare data for DataTables
$data = [];
foreach ($results as $row) {
    $data[] = [
        'id' => $row['id'],
        'UrunKodu' => $row['UrunKodu'],
        'UrunAdiTR' => $row['UrunAdiTR'],
        'title' => $row['title'],
        'category_name' => $row['category_name'] ?: 'Kategori Yok',
        'Vitrin' => $row['Vitrin'],
        'YeniUrun' => $row['YeniUrun'],
        'aktif' => $row['aktif'],
    ];
}

// Debugging: Output memory usage
error_log('Memory usage after processing results: ' . memory_get_usage());

// Return JSON response
$response = [
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalFilteredRecords,
    'data' => $data,
];

// Debugging: Output the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>