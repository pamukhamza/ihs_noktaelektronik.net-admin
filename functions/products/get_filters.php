<?php
// Include database connection
include_once '../db.php';

// Create a new Database instance
$db = new Database();

// Get the category_id from the GET request
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Prepare the SQL statement to fetch filter titles
$sql = "SELECT ft.id, ft.title FROM category_filter_rel cfr 
        JOIN filter_title ft ON cfr.filter_title_id = ft.id 
        WHERE cfr.category_id = :category_id";

// Fetch the results using the Database class
$filters = $db->fetchAll($sql, ['category_id' => $category_id]);

// Return the results as JSON
header('Content-Type: application/json');
echo json_encode($filters);
?>
