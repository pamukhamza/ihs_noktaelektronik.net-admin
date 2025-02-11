<?php
require '../db.php';

$database = new Database();

if(isset($_POST['newOrder'])) {
    $newOrder = $_POST['newOrder'];

    // Loop through the new order and update the database
    foreach($newOrder as $index => $itemId) {
        $query = "UPDATE nokta_urunler SET `sira` = :sort_order WHERE id = :id";
        $params = [
            "sort_order" => $index + 1,
            "id" => $itemId
        ];
        $database->update($query, $params);
    }
    exit();
}
?>
