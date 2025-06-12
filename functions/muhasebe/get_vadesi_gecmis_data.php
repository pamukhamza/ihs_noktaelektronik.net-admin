<?php
include_once '../db.php';

$database = new Database();

// DataTables server-side parameters
$draw = $_POST['draw'];
$start = $_POST['start'];
$length = $_POST['length'];
$search = $_POST['search']['value'];
$order_column = $_POST['order'][0]['column'];
$order_dir = $_POST['order'][0]['dir'];

// Column mapping
$columns = array(
    0 => 'cari_kodu',
    1 => 'ticari_unvani',
    2 => 'yetkilisi',
    3 => 'geciken_tutar',
    4 => 'gerc_vade',
    5 => 'valoru',
    6 => 'email'
);

// Base query
$base_query = "FROM vadesi_gecmis_borc WHERE 1=1";

// Search condition
if (!empty($search)) {
    $base_query .= " AND (cari_kodu LIKE :search 
                      OR ticari_unvani LIKE :search 
                      OR yetkilisi LIKE :search 
                      OR email LIKE :search)";
}

// Order by
$order_by = " ORDER BY " . $columns[$order_column] . " " . $order_dir;

// Get total records
$total_records = $database->fetch("SELECT COUNT(*) as count FROM vadesi_gecmis_borc");
$total_records = $total_records['count'];

// Get filtered records
$filtered_query = "SELECT COUNT(*) as count " . $base_query;
$params = array();
if (!empty($search)) {
    $params[':search'] = '%' . $search . '%';
}
$filtered_records = $database->fetch($filtered_query, $params);
$filtered_records = $filtered_records['count'];

// Get data
$query = "SELECT * " . $base_query . $order_by . " LIMIT :start, :length";
$params[':start'] = (int)$start;
$params[':length'] = (int)$length;

$data = $database->fetchAll($query, $params);

// Prepare response
$response = array(
    "draw" => intval($draw),
    "recordsTotal" => intval($total_records),
    "recordsFiltered" => intval($filtered_records),
    "data" => array()
);

foreach ($data as $row) {
    $response['data'][] = array(
        $row['cari_kodu'],
        $row['ticari_unvani'],
        $row['yetkilisi'],
        number_format($row['geciken_tutar'], 2, ',', '.') . ' ₺',
        $row['gerc_vade'],
        $row['valoru'],
        '<div class="input-group">
            <input type="email" class="form-control email-input" 
                   value="' . htmlspecialchars($row['email'] ?? '') . '" 
                   data-id="' . $row['id'] . '">
            <button class="btn btn-primary update-email" 
                    data-id="' . $row['id'] . '">
                <i class="fas fa-save"></i>
            </button>
        </div>',
        '<button class="btn btn-primary btn-sm send-mail" 
                data-id="' . $row['id'] . '"
                data-email="' . htmlspecialchars($row['email'] ?? '') . '">
            <i class="fas fa-envelope"></i> Mail Gönder
        </button>'
    );
}

echo json_encode($response); 