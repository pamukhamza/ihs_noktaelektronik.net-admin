<?php
include '../db.php';

try {
    $db = new Database();

    // DataTables parametrelerini al
    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';

    // Sıralama bilgilerini al
    $columns = isset($_POST['columns']) ? $_POST['columns'] : [];
    $orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
    $orderDirection = isset($_POST['order'][0]['dir']) && in_array(strtoupper($_POST['order'][0]['dir']), ['ASC', 'DESC']) 
                        ? strtoupper($_POST['order'][0]['dir']) 
                        : 'ASC';

    // Varsayılan sıralama sütunu
    $defaultOrderColumn = "u.id"; 

    // Eğer geçerli bir sütun varsa, onun adına göre sıralama yap
    $orderColumn = isset($columns[$orderColumnIndex]['data']) ? $columns[$orderColumnIndex]['data'] : $defaultOrderColumn;

    // SQL sorgusunu hazırla
    $sql = "SELECT u.*, k.username,  ii.il_adi, ilc.ilce_adi 
            FROM uyeler u 
            LEFT JOIN users k ON u.satis_temsilcisi = k.id
            LEFT JOIN iller ii ON u.il = ii.il_id
            LEFT JOIN ilceler ilc ON u.ilce = ilc.ilce_id
            WHERE 1";

    $params = [];

    // Arama filtresi ekle
    if (!empty($searchValue)) {
        $sql .= " AND (u.muhasebe_kodu LIKE :search OR u.firmaUnvani LIKE :search 
                       OR u.ad LIKE :search OR u.soyad LIKE :search OR u.email LIKE :search 
                       OR k.username LIKE :search OR ii.il_adi LIKE :search OR ilc.ilce_adi LIKE :search)";
        $params['search'] = "%$searchValue%";
    }

    // Sıralama ve sınırlandırma ekleyin
    $sql .= " ORDER BY $orderColumn $orderDirection LIMIT :start, :length";

    // PDO parametreleri ayarla
    $params['start'] = $start;
    $params['length'] = $length;

    // Veriyi çek
    $data = $db->fetchAll($sql, $params);

    // Toplam kayıt sayısını bul
    $totalQuery = "SELECT COUNT(*) AS total FROM uyeler u 
                   LEFT JOIN users k ON u.satis_temsilcisi = k.id
                   WHERE 1";

    $totalParams = []; // Yeni bir dizi oluşturdum

    if (!empty($searchValue)) {
        $totalQuery .= " AND (u.muhasebe_kodu LIKE :search OR u.firmaUnvani LIKE :search 
                              OR u.ad LIKE :search OR u.soyad LIKE :search OR u.email LIKE :search 
                              OR k.username LIKE :search)";
        $totalParams['search'] = "%$searchValue%"; // Sadece burada gerekli olanı ekliyorum
    }

    $total = $db->fetchColumn($totalQuery, $totalParams); // Yeni dizi kullanıldı

    // JSON yanıtı oluştur
    $response = [
        "draw" => $draw,
        "recordsTotal" => $total,
        "recordsFiltered" => $total,
        "data" => $data
    ];

    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    // Hata durumunda JSON hata mesajı döndür
    echo json_encode(["error" => "Database Error: " . $e->getMessage()]);
}
