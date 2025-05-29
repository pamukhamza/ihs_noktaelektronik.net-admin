<?php
require '../db.php';

$database = new Database();

// Get site type from URL parameter
$siteType = isset($_GET['site']) ? $_GET['site'] : 'b2b';
$validSites = ['b2b', 'net', 'b2b-urun'];
if (!in_array($siteType, $validSites)) {
    $siteType = 'b2b';
}

if(isset($_POST['newOrder'])) {
    $newOrder = $_POST['newOrder'];

    // Loop through the new order and update the database
    foreach($newOrder as $index => $itemId) {
        $query = "UPDATE slider SET `order_by` = :sort_order WHERE id = :id AND site = :site_type";
        $params = [
            "sort_order" => $index + 1,
            "id" => $itemId,
            "site_type" => $siteType
        ];
        $database->update($query, $params);
    }
    exit();
}
?>
