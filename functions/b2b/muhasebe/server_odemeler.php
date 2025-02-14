<?php
require_once '../../db.php';
$db = new Database();

// DataTables için gerekli parametreler
$start = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;
$search = $_POST['search']['value'] ?? '';
$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDir = $_POST['order'][0]['dir'] ?? 'asc';
$minTutar = $_POST['minTutar'] ?? '';
$maxTutar = $_POST['maxTutar'] ?? '';
$minTarih = $_POST['minTarih'] ?? '';
$maxTarih = $_POST['maxTarih'] ?? '';
$basarili = $_POST['basarili'] ?? '';
// Sütunlara göre sıralama için eşleşme
$columns = ['pos_id', 'firmaUnvani', 'muhasebe_kodu', 'islem', 'islem_turu', 'tarih', 'tutar', 'basarili', 'islem'];
$orderColumn = $columns[$orderColumnIndex] ?? 'tarih';

// Filtreleme için SQL sorgusu
$where = 'WHERE 1=1';
$params = [];
if (!empty($search)) {
    $where = " AND u.firmaUnvani LIKE :search OR u.muhasebe_kodu LIKE :search ";
    $params['search'] = "%$search%";
}
if ($minTutar !== '') {
    $where .= " AND p.tutar >= :minTutar";
    $params['minTutar'] = (float)$minTutar;
}

if ($maxTutar !== '') {
    $where .= " AND p.tutar <= :maxTutar";
    $params['maxTutar'] = (float)$maxTutar;
}

if ($minTarih !== '') {
    $where .= " AND DATE(p.tarih) >= :minTarih";
    $params['minTarih'] = $minTarih;
}

if ($maxTarih !== '') {
    $where .= " AND DATE(p.tarih) <= :maxTarih";
    $params['maxTarih'] = $maxTarih;
}

if ($basarili !== '') {
    $where .= " AND p.basarili = :basarili";
    $params['basarili'] = $basarili;
}

// Verileri getir
$query = "SELECT p.pos_id, p.uye_id, p.islem_turu, p.tarih, p.tutar, p.basarili, p.islem , u.muhasebe_kodu, u.firmaUnvani, d.dekont
          FROM b2b_sanal_pos_odemeler p
          LEFT JOIN uyeler u ON p.uye_id = u.id
          LEFT JOIN b2b_dekontlar d ON d.pos_odeme_id = p.id
          $where 
          ORDER BY $orderColumn $orderDir 
          LIMIT :start, :length";

$params['start'] = (int)$start;
$params['length'] = (int)$length;

$stmt = $db->fetchAll($query, $params);

// Toplam kayıt sayısı
$totalRecords = $db->fetchColumn("SELECT COUNT(*) FROM b2b_sanal_pos_odemeler");


// DataTables için JSON çıktısı
$data = [];
foreach ($stmt as $row) {
    $durum = $row['basarili'] ? '<span class="badge bg-success">Başarılı</span>' : '<span class="badge bg-danger">Başarısız</span>';
    if ($row['islem_turu'] !== 'cari') {
        $dekont = '-';
    }else{
        $dekont = $row['dekont'] ? "<a href='https://noktanet.s3.eu-central-1.amazonaws.com/uploads/dekont/{$row['dekont']}' target='_blank' class='btn btn-sm btn-info'>Dekont Gör</a>" : '-';
    }
    $posText = match ($row['pos_id']) {
        1 => 'Param Pos',
        2 => 'Garanti Pos',
        3 => 'Kuveyt Pos',
        4 => 'Türkiye Finans Pos',
        default => 'Diğer Poslar'
    };
    $data[] = [
        $posText,
        $row['firmaUnvani'],
        $row['muhasebe_kodu'],
        $row['islem'],
        $row['islem_turu'],
        date('d-m-Y H:i', strtotime($row['tarih'])),
        number_format($row['tutar'], 2, ',', '.')." ₺",
        $durum,
        $dekont
    ];
}

$response = [
    'draw' => intval($_POST['draw'] ?? 1),
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalRecords,
    'data' => $data
];

header('Content-Type: application/json');
echo json_encode($response);
?>
