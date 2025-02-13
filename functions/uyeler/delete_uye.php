<?php
    include ("../db.php"); 
    $gid = $_POST['gid'];
    $gel = $_POST['gel'];
    $type = $_POST['type'];

    if($type == 'uye') {

        $database = new Database();
        $query = "DELETE FROM uyeler WHERE id = :id";
        $success = $database->delete($query, array('id' => $gid));
        exit;
    }
?>