<?php
include_once '../db.php';

$database = new Database();
if (isset($_POST['allData']) && $_POST['allData'] == true) {
    $query = "SELECT p.*, m.id AS mid, m.title, c.KategoriAdiTR AS category_name
    FROM nokta_urunler p
    LEFT JOIN nokta_kategoriler c ON p.KategoriID = c.id
    LEFT JOIN nokta_urun_markalar AS m ON m.id = p.MarkaID";
    $data = $database->fetchAll($query);
    ob_clean(); // Daha önceki tamponları temizler
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
    SELECT p.*, m.id AS mid, m.title, c.KategoriAdiTR AS category_name
    FROM nokta_urunler p
    LEFT JOIN nokta_kategoriler c ON p.KategoriID = c.id
    LEFT JOIN nokta_urun_markalar AS m ON m.id = p.MarkaID
";

// Add search functionality
if (!empty($searchValue)) {
    $query .= " WHERE p.UrunAdiTR LIKE '%" . $searchValue . "%' 
                OR p.UrunKodu LIKE '%" . $searchValue . "%' 
                OR m.title LIKE '%" . $searchValue . "%' 
                OR c.KategoriAdiTR LIKE '%" . $searchValue . "%'";
}

// Get total records without filtering
$totalRecordsQuery = "SELECT COUNT(*) as total FROM nokta_urunler";
$totalRecordsResult = $database->fetchAll($totalRecordsQuery);
$totalRecords = $totalRecordsResult[0]['total'];

// Get filtered records
$filteredRecordsQuery = $query;
$filteredRecordsResult = $database->fetchAll($filteredRecordsQuery);
$totalFilteredRecords = count($filteredRecordsResult);

// Add pagination
$query .= " LIMIT $start, $length";
$results = $database->fetchAll($query);

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

// Return JSON response
$response = [
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalFilteredRecords,
    'data' => $data,
];

echo json_encode($response);
?>