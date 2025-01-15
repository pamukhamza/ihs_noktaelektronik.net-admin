<?php
// Include database connection
include_once '../db.php';

// Create a new Database instance
$db = new Database();

// Get the filter_title_id from the GET request
$filter_title_id = isset($_GET['filter_title_id']) ? intval($_GET['filter_title_id']) : 0;

// Prepare the SQL statement to fetch filter values
$sql = "SELECT * FROM filter_value WHERE filter_title_id = :filter_title_id";

// Fetch the results using the Database class
$values = $db->fetchAll($sql, ['filter_title_id' => $filter_title_id]);

// Return the results as JSON
header('Content-Type: application/json');
echo json_encode($values);
?>
