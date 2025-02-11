<?php
include "../../db.php";
$database = new Database();
$sql = "SELECT id, BLBNHSKODU, BANKA_ADI, TANIMI, TAKSIT_SAYISI FROM b2b_banka_pos_listesi";
$data = $database->fetchAll($sql);
echo json_encode($data);
?>
