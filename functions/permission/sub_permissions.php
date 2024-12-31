<?php
include_once '../db.php';
$database = new Database();
if (isset($_POST['parent_id'])) {
    $parent_id = intval($_POST['parent_id']);
    $query = "SELECT * FROM permissions WHERE parent_id = $parent_id";
    $subPermissions = $database->fetchAll($query);
    echo json_encode($subPermissions);
}
?>
