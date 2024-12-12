<?php
// Include database connection
include_once '../db.php';

// Create a new Database instance
$db = new Database();

// Get the input data
$input = json_decode(file_get_contents('php://input'), true);
$product_id = isset($input['product_id']) ? intval($input['product_id']) : 0;
$filter_value_id = isset($input['filter_value_id']) ? intval($input['filter_value_id']) : 0;

// Prepare the SQL statement to insert data
$sql = "INSERT INTO products_filter_rel (product_id, filter_value_id) VALUES (:product_id, :filter_value_id)";

// Insert the data using the Database class
$success = $db->insert($sql, ['product_id' => $product_id, 'filter_value_id' => $filter_value_id]);

// Return a JSON response
header('Content-Type: application/json');
if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add filter.']);
}
?>
