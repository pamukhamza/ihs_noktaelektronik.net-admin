<?php
include_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = $_POST['image'] ?? '';
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if ($image && $productId) {
        $database = new Database();

        // Delete the image from the database
        $deleteQuery = "DELETE FROM product_images WHERE image = :image AND prod_id = :product_id";
        $params = [
            'image' => $image,
            'product_id' => $productId,
        ];
        $database->insert($deleteQuery, $params); // Use insert method for delete operation

        // Delete the image from the filesystem
        $filePath = "../../assets/images/products/" . $image;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Update sort_order for remaining images
        $updateQuery = "SELECT id FROM product_images WHERE prod_id = :product_id ORDER BY sort_order";
        $updateParams = ['product_id' => $productId];
        $remainingImages = $database->fetchAll($updateQuery, $updateParams);

        foreach ($remainingImages as $index => $img) {
            $newSortOrder = $index + 1; // New sort order starts from 1
            $updateSortOrderQuery = "UPDATE product_images SET sort_order = :sort_order WHERE id = :id";
            $updateSortOrderParams = [
                'sort_order' => $newSortOrder,
                'id' => $img['id'],
            ];
            $database->insert($updateSortOrderQuery, $updateSortOrderParams);
        }

        // Return a JSON response indicating success
        echo json_encode(["status" => "success", "message" => "Image deleted successfully."]);
    } else {
        // Return a JSON response for invalid request
        echo json_encode(["status" => "error", "message" => "Invalid request."]);
    }
} else {
    // Return a JSON response for invalid request method
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
