<?php
include_once '../db.php';
require '../../vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$config = require '../../aws-config.php';

if (!isset($config['s3']['region']) || !isset($config['s3']['key']) || !isset($config['s3']['secret']) || !isset($config['s3']['bucket'])) {
    die('Missing required S3 configuration values.');
}

$s3Client = new S3Client([
    'version' => 'latest',
    'region'  => $config['s3']['region'],
    'credentials' => [
        'key'    => $config['s3']['key'],
        'secret' => $config['s3']['secret'],
    ]
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = $_POST['image'] ?? '';
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if ($image && $productId) {
        $database = new Database();

        // Delete the image from the database
        $deleteQuery = "DELETE FROM nokta_urunler_resimler WHERE KResim = :image AND UrunID = :product_id";
        $params = [
            'image' => $image,
            'product_id' => $productId,
        ];
        $database->insert($deleteQuery, $params); // Use insert method for delete operation

        // Delete the image from S3
        $filePath = 'uploads/images/products/' . $image;
        try {
            $result = $s3Client->deleteObject([
                'Bucket' => $config['s3']['bucket'],
                'Key'    => $filePath,
            ]);
        } catch (AwsException $e) {
            echo json_encode(["status" => "error", "message" => "Error deleting file from S3: " . $e->getMessage()]);
            exit;
        }

        // Update sort_order for remaining images
        $updateQuery = "SELECT id FROM nokta_urunler_resimler WHERE UrunID = :product_id ORDER BY Sira";
        $updateParams = ['product_id' => $productId];
        $remainingImages = $database->fetchAll($updateQuery, $updateParams);

        foreach ($remainingImages as $index => $img) {
            $newSortOrder = $index + 1; // New sort order starts from 1
            $updateSortOrderQuery = "UPDATE nokta_urunler_resimler SET Sira = :sort_order WHERE id = :id";
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