<?php 
include_once '../../db.php';
$database = new Database();

function getTeklif($iId) {
    global $database;

    $query = "SELECT bt.*, u.* FROM `b2b_teklif` AS bt
    LEFT JOIN uyeler AS u ON bt.uye_id = u.id
    WHERE bt.id = :id";

    $paramsParent = ["id" => $iId];
    $teklif = $database->fetch($query, $paramsParent);

    return $teklif;
}

if (isset($_POST['id']) && isset($_POST['type'])) {
    $id = $_POST['id'];
    $type = $_POST['type'];

    if ($type === 'teklif') {
        $data = getTeklif($id);
    } else {
        $data = ['error' => 'Invalid type'];
    }
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Invalid request']);
}