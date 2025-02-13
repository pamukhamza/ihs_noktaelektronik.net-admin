<?php
include_once '../../db.php';

$database = new Database();

$columns = ['pos_id', 'firmaUnvani', 'muhasebe_kodu', 'islem', 'tarih', 'tutar', 'basarili', 'dekont'];

// Filtreleme parametreleri
$minTutar = $_POST['minTutar'] ?? '';
$maxTutar = $_POST['maxTutar'] ?? '';
$minTarih = $_POST['minTarih'] ?? '';
$maxTarih = $_POST['maxTarih'] ?? '';
$basarili = $_POST['basarili'] ?? '';

// Sorgu başlangıcı
$sql = "SELECT b.id, b.uye_id, b.pos_id, b.islem, b.islem_turu, b.tutar, b.basarili, b.tarih, u.firmaUnvani, u.muhasebe_kodu, u.tel, d.islem_no, d.dekont FROM b2b_sanal_pos_odemeler b
LEFT JOIN uyeler u ON b.uye_id = u.id
LEFT JOIN b2b_dekontlar d ON b.id = d.pos_odeme_id
WHERE 1=1";

// Dinamik filtreleme
$params = [];
if (!empty($minTutar)) {
    $sql .= " AND b.tutar >= :minTutar";
    $params['minTutar'] = $minTutar;
}
if (!empty($maxTutar)) {
    $sql .= " AND b.tutar <= :maxTutar";
    $params['maxTutar'] = $maxTutar;
}
if (!empty($minTarih)) {
    $sql .= " AND b.tarih >= :minTarih";
    $params['minTarih'] = $minTarih;
}
if (!empty($maxTarih)) {
    $sql .= " AND b.tarih <= :maxTarih";
    $params['maxTarih'] = $maxTarih;
}
if ($basarili !== '') {
    $sql .= " AND b.basarili = :basarili";
    $params['basarili'] = $basarili;
}

// Sıralama
$orderBy = $columns[$_POST['order'][0]['column']] ?? 'b.tarih';
$orderDir = $_POST['order'][0]['dir'] ?? 'DESC';
$sql .= " ORDER BY $orderBy $orderDir";

// Sayfalama
$start = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;
$sql .= " LIMIT :start, :length";
$params['start'] = (int)$start;
$params['length'] = (int)$length;

// Verileri çek
try {
    $data = $database->fetchAll($sql, $params);

    // Toplam kayıt sayısını al
    $totalRecords = $database->fetchColumn("SELECT COUNT(*) FROM b2b_sanal_pos_odemeler");

    // Filtrelenmiş kayıt sayısı
    $filterSql = "SELECT COUNT(*) FROM b2b_sanal_pos_odemeler b
    LEFT JOIN uyeler u ON b.uye_id = u.id
    LEFT JOIN b2b_dekontlar d ON b.id = d.pos_odeme_id
    WHERE 1=1";
    foreach ($params as $key => $value) {
        if ($key !== 'start' && $key !== 'length') {
            $filterSql .= " AND b." . str_replace(':', '', $key) . " = :$key";
        }
    }
    $filteredRecords = $database->fetchColumn($filterSql, $params);

    $response = [
        "draw" => $_POST['draw'],
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $filteredRecords,
        "data" => $data
    ];
} catch (Exception $e) {
    $response = [
        "error" => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
