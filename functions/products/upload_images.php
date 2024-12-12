<?php
include_once '../db.php';

$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$targetDir = "../../assets/images/products/";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
    $uploadedFiles = [];
    $errors = [];

    $database = new Database();

    // Check for existing images and get the max sort_order
    $maxSortOrderQuery = "SELECT MAX(sort_order) AS max_order FROM product_images WHERE prod_id = :product_id";
    $maxSortOrderParams = ['product_id' => $productId];
    $maxSortOrderResult = $database->fetch($maxSortOrderQuery, $maxSortOrderParams);
    $newSortOrder = isset($maxSortOrderResult['max_order']) ? $maxSortOrderResult['max_order'] + 1 : 1;

    foreach ($_FILES['images']['name'] as $key => $name) {
        $targetFilePath = $targetDir . time() . '_' . basename($name); // Unique filename

        // Validate file type and size
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['images']['type'][$key];

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Invalid file type: " . $name;
            continue; // Skip this file
        }

        if ($_FILES['images']['size'][$key] > 2000000) { // 2MB limit
            $errors[] = "File size exceeds limit: " . $name;
            continue; // Skip this file
        }

        // Move the uploaded file
        if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $targetFilePath)) {
            $uploadedFiles[] = basename($targetFilePath);

            // Insert image name, product ID, and sort_order into the database
            $sql = "INSERT INTO product_images (prod_id, image, sort_order) VALUES (:product_id, :image_name, :sort_order)";
            $params = [
                'product_id' => $productId,
                'image_name' => basename($targetFilePath),
                'sort_order' => $newSortOrder
            ];
            $database->insert($sql, $params);

            // Increment sort_order for the next image
            $newSortOrder++;
        } else {
            $errors[] = "Error uploading file: " . $_FILES['images']['error'][$key];
        }
    }

    if (!empty($errors)) {
        echo json_encode(["status" => "error", "messages" => $errors]);
    } else {
        echo json_encode(["status" => "success", "files" => $uploadedFiles]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No files uploaded."]);
}
?>
