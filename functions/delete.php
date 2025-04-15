<?php
include ("db.php"); 
    $database = new Database();
    $gid = $_POST['gid'];
    $gel = $_POST['gel'];
    $type = $_POST['type'];
    $file = $_POST['file'];

if($type == 'banka') {
    $delete = $database->delete("DELETE FROM banka WHERE id = :id", ['id' => $gid]);
    exit;
}elseif ($type == 'teknik-servis') {
    $delete = $database->update("UPDATE teknik_destek_urunler SET SILINDI = 1 WHERE id = :id", ['id' => $gid]);
    exit;
}
