<?php
include_once '../db.php';
$database = new Database();

// Initialize variables
$start = $_GET['start'] ?? 0; // Starting point for fetching records
$length = $_GET['length'] ?? 10; // Number of records to fetch
$search_value = $_GET['search']['value'] ?? ''; // Search value if any
$order_column = $_GET['order'][0]['column'] ?? 0; // Column index for ordering
$order_dir = $_GET['order'][0]['dir'] ?? 'asc'; // Order direction (asc or desc)
$filter_technician = $_GET['technician'] ?? ''; // Technician filter
$filter_status = $_GET['sDurum'] ?? ''; // sDurum filter
$start_date = $_GET['start_date'] ?? ''; // Start date filter
$end_date = $_GET['end_date'] ?? ''; // End date filter
$seri_no_ara = $_GET['seri_no_ara'] ?? ''; 

// Define your SQL query for total records
$sql = "SELECT COUNT(*) AS total_records FROM teknik_destek_urunler WHERE SILINDI = 0";
$total_records = $database->fetchColumn($sql);

// Define your base SQL query for fetching records with JOIN
$sql = "
    SELECT 
        t.takip_kodu, 
        t.musteri, 
        t.tarih, 
        t.id, 
        t.tel, 
        u.*, 
        d.durum
    FROM teknik_destek_urunler u
    JOIN nokta_teknik_destek t ON u.tdp_id = t.id
    LEFT JOIN nokta_teknik_durum d ON u.urun_durumu = d.id
    WHERE u.SILINDI = 0
";

// Prepare parameters for the query
$params = [];

// Add search filter if there's a search parameter
if (!empty($search_value)) {
    $sql .= " AND (t.takip_kodu LIKE :search 
              OR u.urun_kodu LIKE :search 
              OR t.musteri LIKE :search 
              OR t.tarih LIKE :search 
              OR t.tel LIKE :search 
              OR u.tekniker LIKE :search)";
    $params['search'] = "%$search_value%";
}

// Add technician filter if selected
if (!empty($filter_technician)) {
    $sql .= " AND u.tekniker LIKE :technician";
    $params['technician'] = "%$filter_technician%";
}
if (!empty($seri_no_ara)) {
    $sql .= " AND u.seri_no LIKE :seri_no";
    $params['seri_no'] = "%$seri_no_ara%";
}
// Add status filter
if ($filter_status == '1') {
    $sql .= " AND u.urun_durumu = :status";
    $params['status'] = 1;
} elseif ($filter_status == '2') {
    $sql .= " AND u.urun_durumu = :status";
    $params['status'] = 2;
} elseif ($filter_status == '3') {
    $sql .= " AND u.urun_durumu = :status";
    $params['status'] = 3;
} elseif ($filter_status == '4') {
    $sql .= " AND u.urun_durumu NOT IN (1, 2, 3)";
} elseif ($filter_status == '0') {
    $sql .= " AND u.urun_durumu IN (1, 2, 3)";
}

// Add date range filter if both start and end dates are provided
if (!empty($start_date) && !empty($end_date)) {
    $sql .= " AND t.tarih BETWEEN :start_date AND :end_date";
    $params['start_date'] = $start_date;
    $params['end_date'] = $end_date;
}

// Define order by clause
$order_columns = ['t.takip_kodu', 'u.urun_kodu', 't.musteri', 't.tarih', 'u.tekniker', 'd.durum'];
$order_by = $order_columns[$order_column];
$sql .= " ORDER BY $order_by $order_dir";

// Add limit for pagination
$sql .= " LIMIT :start, :length";
$params['start'] = (int)$start;
$params['length'] = (int)$length;

// Execute the query to fetch records
$data = $database->fetchAll($sql, $params);

// Define your SQL query for filtered records count
$filtered_records_sql = "
    SELECT COUNT(*) AS filtered_records 
    FROM teknik_destek_urunler u
    JOIN nokta_teknik_destek t ON u.tdp_id = t.id
    LEFT JOIN nokta_teknik_durum d ON u.urun_durumu = d.id
    WHERE u.SILINDI = 0
";

// Remove pagination parameters as they're not needed for count
unset($params['start'], $params['length']);

// Add the same WHERE conditions as the main query
if (!empty($search_value)) {
    $filtered_records_sql .= " AND (t.takip_kodu LIKE :search 
                               OR u.urun_kodu LIKE :search 
                               OR t.musteri LIKE :search 
                               OR t.tarih LIKE :search 
                               OR t.tel LIKE :search
                               OR u.tekniker LIKE :search)";
}

if (!empty($filter_technician)) {
    $filtered_records_sql .= " AND u.tekniker LIKE :technician";
}

if ($filter_status == '1' || $filter_status == '2' || $filter_status == '3') {
    $filtered_records_sql .= " AND u.urun_durumu = :status";
} elseif ($filter_status == '4') {
    $filtered_records_sql .= " AND u.urun_durumu NOT IN (1, 2, 3)";
} elseif ($filter_status == '0') {
    $filtered_records_sql .= " AND u.urun_durumu IN (1, 2, 3)";
}

if (!empty($start_date) && !empty($end_date)) {
    $filtered_records_sql .= " AND t.tarih BETWEEN :start_date AND :end_date";
}

// Execute the query to get filtered records count
$filtered_records = $database->fetchColumn($filtered_records_sql, $params);

// Output the JSON data
$response = array(
    "draw" => intval($_GET['draw']),
    "recordsTotal" => intval($total_records),
    "recordsFiltered" => intval($filtered_records),
    "data" => $data
);

echo json_encode($response);
?>