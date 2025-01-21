<?php
include_once '../db.php';

$database = new Database();
$start_time = microtime(true);

// Get parameters from DataTables
$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);
$searchValue = $_POST['search']['value'];
$orderColumnIndex = intval($_POST['order'][0]['column']);
$orderColumnDir = $_POST['order'][0]['dir'];
$orderColumn = $_POST['columns'][$orderColumnIndex]['data'];

// Get total records count (without filtering)
$totalRecordsQuery = "SELECT COUNT(*) as total FROM nokta_urunler";
$totalRecordsResult = $database->fetchAll($totalRecordsQuery);
$totalRecords = $totalRecordsResult[0]['total'];

// Build main query for data
$mainQuery = "
    SELECT SQL_CALC_FOUND_ROWS p.id, p.UrunKodu, p.UrunAdiTR, p.web_net, p.web_comtr, p.web_cn,
           m.id AS mid, m.title, 
           c.KategoriAdiTR AS category_name, 
           p.Vitrin, p.YeniUrun, p.aktif,
           r.KResim
    FROM nokta_urunler p
    LEFT JOIN nokta_kategoriler c ON p.KategoriID = c.id
    LEFT JOIN nokta_urun_markalar AS m ON m.id = p.MarkaID
    LEFT JOIN nokta_urunler_resimler r ON p.id = r.UrunID
";

// Add search functionality
$params = [];
if (!empty($searchValue)) {
    $mainQuery .= " WHERE p.UrunAdiTR LIKE :search 
                    OR p.UrunKodu LIKE :search 
                    OR m.title LIKE :search 
                    OR c.KategoriAdiTR LIKE :search";
    $params['search'] = '%' . $searchValue . '%';
}

// Add sorting
$mainQuery .= " ORDER BY $orderColumn $orderColumnDir";

// Add pagination
$mainQuery .= " LIMIT :start, :length";
$params['start'] = $start;
$params['length'] = $length;

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
        'aktif' => $row['aktif'],
        'web_net' => $row['web_net'],
        'web_comtr' => $row['web_comtr'],
        'web_cn' => $row['web_cn'],
        'KResim' => $row['KResim']
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
?>