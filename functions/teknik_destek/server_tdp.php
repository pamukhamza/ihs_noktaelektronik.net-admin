<?php
require_once '../db.php';
$database = new Database();

$start = $_GET['start'] ?? 0;
$length = $_GET['length'] ?? 10;
$search_value = $_GET['search']['value'] ?? '';
$order_column = $_GET['order'][0]['column'] ?? 0;
$order_dir = $_GET['order'][0]['dir'] ?? 'asc';
$filter_technician = $_GET['technician'] ?? '';
$filter_status = $_GET['sDurum'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Toplam kayıt sayısını al
$total_records = $database->fetch("SELECT COUNT(*) FROM teknik_destek_urunler");

// Ana sorgu
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

$params = [];

// Arama filtresi
if (!empty($search_value)) {
    $sql .= " AND (t.takip_kodu LIKE :search_value OR u.urun_kodu LIKE :search_value OR t.musteri LIKE :search_value OR t.tarih LIKE :search_value 
    OR t.tel LIKE :search_value OR u.tekniker LIKE :search_value)";
    $params['search_value'] = "%$search_value%";
}

// Tekniker filtresi
if (!empty($filter_technician)) {
    $sql .= " AND u.tekniker = :filter_technician";
    $params['filter_technician'] = $filter_technician;
}

// Durum filtresi
if ($filter_status !== '') {
    if ($filter_status == '4') {
        $sql .= " AND u.urun_durumu NOT IN (1, 2, 3)";
    } elseif ($filter_status == '0') {
        $sql .= " AND u.urun_durumu IN (1, 2, 3)";
    } else {
        $sql .= " AND u.urun_durumu = :filter_status";
        $params['filter_status'] = $filter_status;
    }
}

// Tarih filtresi
if (!empty($start_date) && !empty($end_date)) {
    $sql .= " AND t.tarih BETWEEN :start_date AND :end_date";
    $params['start_date'] = $start_date;
    $params['end_date'] = $end_date;
}

// Sıralama
$order_columns = ['t.takip_kodu', 'u.urun_kodu', 't.musteri', 't.tarih', 'u.tekniker', 'd.durum'];
$order_by = $order_columns[$order_column] ?? 't.takip_kodu';
$sql .= " ORDER BY $order_by $order_dir";

// Limit
$sql .= " LIMIT :start, :length";
$params['start'] = (int)$start;
$params['length'] = (int)$length;

// Kayıtları çek
$data = $database->fetchAll($sql, $params);

// Filtrelenmiş kayıt sayısını al
$filtered_records = $database->fetchColumn("
    SELECT COUNT(*) 
    FROM teknik_destek_urunler u
    JOIN nokta_teknik_destek t ON u.tdp_id = t.id
    LEFT JOIN nokta_teknik_durum d ON u.urun_durumu = d.id
    WHERE u.SILINDI = 0
", $params);

// JSON cevabı
$response = [
    "draw" => intval($_GET['draw']),
    "recordsTotal" => intval($total_records),
    "recordsFiltered" => intval($filtered_records),
    "data" => $data
];

echo json_encode($response);
?>