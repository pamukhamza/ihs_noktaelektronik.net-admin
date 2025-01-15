<?php
// Include database connection
include_once '../db.php';

// Create a new Database instance
$db = new Database();

// Get the category_id from the GET request
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Function to get filters for a category and its parent categories
function getFilters($db, $category_id) {
    $filters = [];

    // Prepare the SQL statement to fetch filter titles
    $sql = "SELECT ft.id, ft.title FROM category_filter_rel cfr 
            JOIN filter_title ft ON cfr.filter_title_id = ft.id 
            WHERE cfr.category_id = :category_id";

    // Fetch the results using the Database class
    $categoryFilters = $db->fetchAll($sql, ['category_id' => $category_id]);
    $filters = array_merge($filters, $categoryFilters);

    // Get the parent category ID
    $parentSql = "SELECT * FROM nokta_kategoriler WHERE id = :category_id";
    $parentCategory = $db->fetch($parentSql, ['category_id' => $category_id]);

    if ($parentCategory && $parentCategory['parent_id'] != 0) {
        // Recursively get filters for the parent category
        $parentFilters = getFilters($db, $parentCategory['parent_id']);
        $filters = array_merge($filters, $parentFilters);
    }

    return $filters;
}

// Get filters for the category and its parent categories
$filters = getFilters($db, $category_id);

// Return the results as JSON
header('Content-Type: application/json');
echo json_encode($filters);
?>