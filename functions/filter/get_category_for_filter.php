<?php
include_once '../db.php';
$database = new Database();

// AJAX isteğinden gelen filter_title_id değerini alıyoruz
$filter_title_id = $_GET['filter_title_id'];

// Kategori ve ilişkiyi almak için sorgu yapıyoruz
// Prepare the SQL query
$query = "SELECT category_id FROM category_filter_rel WHERE filter_title_id = $filter_title_id LIMIT 1";


// Fetch the result
$result = $database->fetch($query);


// JSON formatında sonuç döndürüyoruz
echo json_encode(['category_id' => $result['category_id']]);
?>
