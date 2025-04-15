<?php
include '../db.php';

try {
    $db = new Database();

    // DataTables parametreleri
    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
    $satis_id = $_POST['satis_id'];

    // Sıralama bilgileri
    $columns = $_POST['columns'] ?? [];
    $orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
    $orderDirection = in_array(strtoupper($_POST['order'][0]['dir'] ?? ''), ['ASC', 'DESC']) ? strtoupper($_POST['order'][0]['dir']) : 'ASC';
    $defaultOrderColumn = "u.id";
    $orderColumn = $columns[$orderColumnIndex]['data'] ?? $defaultOrderColumn;

    // Verileri çekmek için SQL sorgusu
    $sql = "SELECT u.*, k.username, ii.il_adi, ilc.ilce_adi 
            FROM uyeler u 
            LEFT JOIN users k ON u.satis_temsilcisi = k.id
            LEFT JOIN iller ii ON u.il = ii.il_id
            LEFT JOIN ilceler ilc ON u.ilce = ilc.ilce_id
            WHERE u.satis_temsilcisi = :satis_id";

    $params = ['satis_id' => $satis_id];

    if (!empty($searchValue)) {
        $sql .= " AND (
            u.muhasebe_kodu LIKE :search OR 
            u.firmaUnvani LIKE :search OR 
            u.ad LIKE :search OR 
            u.soyad LIKE :search OR 
            u.email LIKE :search OR 
            k.username LIKE :search OR 
            ii.il_adi LIKE :search OR 
            ilc.ilce_adi LIKE :search
        )";
        $params['search'] = "%$searchValue%";
    }

    // Sıralama ve limit
    $sql .= " ORDER BY $orderColumn $orderDirection LIMIT :start, :length";
    $params['start'] = $start;
    $params['length'] = $length;

    // Verileri getir
    $data = $db->fetchAll($sql, $params);

    // Toplam kayıt sayısı
    $countSql = "SELECT COUNT(*) AS total FROM uyeler u 
                 LEFT JOIN users k ON u.satis_temsilcisi = k.id
                 LEFT JOIN iller ii ON u.il = ii.il_id
                 LEFT JOIN ilceler ilc ON u.ilce = ilc.ilce_id
                 WHERE u.satis_temsilcisi = :satis_id";

    $countParams = ['satis_id' => $satis_id];

    if (!empty($searchValue)) {
        $countSql .= " AND (
            u.muhasebe_kodu LIKE :search OR 
            u.firmaUnvani LIKE :search OR 
            u.ad LIKE :search OR 
            u.soyad LIKE :search OR 
            u.email LIKE :search OR 
            k.username LIKE :search OR 
            ii.il_adi LIKE :search OR 
            ilc.ilce_adi LIKE :search
        )";
        $countParams['search'] = "%$searchValue%";
    }

    $total = $db->fetchColumn($countSql, $countParams);

    // JSON yanıtı oluştur
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => $total,
        "recordsFiltered" => $total,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    echo json_encode(["error" => "Veritabanı Hatası: " . $e->getMessage()]);
}
