<?php
include_once '../db.php';
require '../../vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$config = require '../../config/aws-config.php';

if (!isset($config['s3']['region']) || !isset($config['s3']['key']) || !isset($config['s3']['secret']) || !isset($config['s3']['bucket'])) {
    die('Missing required S3 configuration values.');
}

$s3Client = new S3Client([
    'version' => 'latest',
    'region'  => $config['s3']['region'],
    'credentials' => [
        'key'    => $config['s3']['key'],
        'secret' => $config['s3']['secret'],
    ],
]);

$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
    $uploadedFiles = [];
    $errors = [];

    $database = new Database();

    // Check for existing images and get the max sort_order
    $maxSortOrderQuery = "SELECT MAX(Sira) AS sira FROM nokta_urunler_resimler WHERE UrunID = :product_id";
    $maxSortOrderParams = ['product_id' => $productId];
    $maxSortOrderResult = $database->fetch($maxSortOrderQuery, $maxSortOrderParams);
    $newSortOrder = isset($maxSortOrderResult['sira']) ? $maxSortOrderResult['sira'] + 1 : 1;

    foreach ($_FILES['images']['name'] as $key => $name) {
        $fileName = time() . '_' . basename($name); // Unique filename
        $targetFilePath = 'uploads/images/products/' . $fileName;

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

        // Upload the file to S3
        try {
            $result = $s3Client->putObject([
                'Bucket' => $config['s3']['bucket'],
                'Key'    => $targetFilePath,
                'SourceFile' => $_FILES['images']['tmp_name'][$key],
                'ACL'    => 'public-read', // Optional: make the file publicly accessible
            ]);

            $uploadedFiles[] = $result['ObjectURL'];

            // Insert image name, product ID, and sort_order into the database
            $sql = "INSERT INTO nokta_urunler_resimler (UrunID, KResim, sira) VALUES (:product_id, :image_name, :sort_order)";
            $params = [
                'product_id' => $productId,
                'image_name' => $fileName,
                'sort_order' => $newSortOrder
            ];
            $database->insert($sql, $params);

            // Increment sort_order for the next image
            $newSortOrder++;
        } catch (AwsException $e) {
            $errors[] = "Error uploading file: " . $e->getMessage();
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