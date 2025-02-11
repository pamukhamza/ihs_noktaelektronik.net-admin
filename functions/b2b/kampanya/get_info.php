<?php
include_once '../../db.php';
$database = new Database();
function getPopupById($sId) {
    global $database;
    $query = "SELECT * FROM b2b_popup_kampanya WHERE id = :id";
    $params = ["id" => $sId];
    $popup = $database->fetch($query, $params);

    return $popup;
}
function getKampanyaById($vId) {
    global $database;
    $query = "SELECT * FROM b2b_kampanyalar WHERE id = :id";
    $params = ["id" => $vId];
    $var = $database->fetch($query, $params);
    return $var;
}
if (isset($_POST['id']) && isset($_POST['type'])) {
    $id = $_POST['id'];
    $type = $_POST['type'];
    if ($type === 'kampanya') {
      $data = getKampanyaById($id);
    } elseif ($type === 'popup') {
        $data = getPopupById($id);
    }
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>