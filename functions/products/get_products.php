<?php
include_once '../db.php';

$database = new Database();
$start_time = microtime(true);

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

// Build the query with index hints
$query = "
    SELECT SQL_CALC_FOUND_ROWS p.id, p.UrunKodu, p.UrunAdiTR, m.id AS mid, m.title, c.KategoriAdiTR AS category_name, p.Vitrin, p.YeniUrun, p.aktif
    FROM nokta_urunler p USE INDEX (PRIMARY)
    LEFT JOIN nokta_kategoriler c USE INDEX (PRIMARY) ON p.KategoriID = c.id
    LEFT JOIN nokta_urun_markalar AS m USE INDEX (PRIMARY) ON m.id = p.MarkaID
";

// Add search functionality with optimized WHERE clause
$params = [];
if (!empty($searchValue)) {
    $query .= " WHERE (p.UrunAdiTR LIKE :search1 
                OR p.UrunKodu LIKE :search2 
                OR m.title LIKE :search3 
                OR c.KategoriAdiTR LIKE :search4)";
    $searchParam = '%' . $searchValue . '%';
    $params['search1'] = $searchParam;
    $params['search2'] = $searchParam;
    $params['search3'] = $searchParam;
    $params['search4'] = $searchParam;
}

error_log("Before total records query: " . (microtime(true) - $start_time));

// Get total records without filtering - use COUNT(*) with index
$totalRecordsQuery = "SELECT COUNT(*) as total FROM nokta_urunler p USE INDEX (PRIMARY)";
$totalRecordsResult = $database->fetchAll($totalRecordsQuery);
$totalRecords = $totalRecordsResult[0]['total'];

error_log("After total records query: " . (microtime(true) - $start_time));

// Add pagination
$query .= " LIMIT " . intval($start) . ", " . intval($length);

// Get the results
error_log("Before main query: " . (microtime(true) - $start_time));
$results = $database->fetchAll($query, $params);
error_log("After main query: " . (microtime(true) - $start_time));

// Get total filtered records
$filteredRecordsQuery = "SELECT FOUND_ROWS() as total";
$filteredResult = $database->fetchAll($filteredRecordsQuery);
$totalFilteredRecords = $filteredResult[0]['total'];

error_log("After filtered count query: " . (microtime(true) - $start_time));

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

error_log("After data preparation: " . (microtime(true) - $start_time));

// Return JSON response
$response = [
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalFilteredRecords,
    'data' => $data,
];

// Debugging: Output memory usage
error_log('Memory usage after processing results: ' . memory_get_usage());

header('Content-Type: application/json');
echo json_encode($response);
error_log("Total execution time: " . (microtime(true) - $start_time));
?>