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

// Get total records count (without filtering)
$totalRecordsQuery = "SELECT COUNT(*) as total FROM nokta_urunler";
$totalRecordsResult = $database->fetchAll($totalRecordsQuery);
$totalRecords = $totalRecordsResult[0]['total'];

// Build main query for data
$mainQuery = "
    SELECT SQL_CALC_FOUND_ROWS 
        p.id, p.UrunKodu, p.UrunAdiTR, 
        m.id AS mid, m.title, 
        c.KategoriAdiTR AS category_name, 
        p.Vitrin, p.YeniUrun, p.aktif
    FROM nokta_urunler p
    LEFT JOIN nokta_kategoriler c ON p.KategoriID = c.id
    LEFT JOIN nokta_urun_markalar AS m ON m.id = p.MarkaID
";

// Add search functionality
$params = [];
if (!empty($searchValue)) {
    $mainQuery .= " WHERE (p.UrunAdiTR LIKE :search1 
                OR p.UrunKodu LIKE :search2 
                OR m.title LIKE :search3 
                OR c.KategoriAdiTR LIKE :search4)";
    $searchParam = '%' . $searchValue . '%';
    $params['search1'] = $searchParam;
    $params['search2'] = $searchParam;
    $params['search3'] = $searchParam;
    $params['search4'] = $searchParam;
}

// Add sorting
$mainQuery .= " ORDER BY p.id DESC";

// Add pagination
$mainQuery .= " LIMIT " . intval($start) . ", " . intval($length);

// Execute main query
$results = $database->fetchAll($mainQuery, $params);

// Get filtered count
$filteredCountQuery = "SELECT FOUND_ROWS() as total";
$filteredResult = $database->fetchAll($filteredCountQuery);
$totalFilteredRecords = $filteredResult[0]['total'];

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
        'aktif' => $row['aktif']
    ];
}

// Return JSON response
$response = [
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalFilteredRecords,
    'data' => $data
];

header('Content-Type: application/json');
echo json_encode($response);
error_log("Total execution time: " . (microtime(true) - $start_time));
?>