<?php
include_once '../db.php';
require '../../vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$database = new Database(); // Using your Database class

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

if (isset($_POST["urun_dosya_ekle"])) {
    $urun_id = $_POST['id'];
    $dosya_turu = $_POST['dosya_turu'];
    $dosya_aciklama = $_POST['dosya_aciklama'];
    $dosya_aciklama_EN = $_POST['dosya_aciklama_EN'];
    $dosya_versiyon = $_POST['dosya_versiyon'];
    $dosya_tarihi = $_POST['dosya_tarihi'];

    // Check if file was uploaded
    if (!isset($_FILES['dosya']) || $_FILES['dosya']['error'] !== UPLOAD_ERR_OK) {
        die("Dosya yükleme hatası: " . ($_FILES['dosya']['error'] ?? 'Dosya seçilmedi'));
    }

    $dosya = $_FILES['dosya']['name'];
    $ext = pathinfo($dosya, PATHINFO_EXTENSION);

    // Get upload path from database
    $query_select = "SELECT dosya_yolu FROM nokta_yuklemeler WHERE id = :id";
    $result = $database->fetch($query_select, ['id' => $dosya_turu]);

    if (!$result) {
        die("Dosya türü bulunamadı.");
    }

    $dosya_yolu = ltrim($result["dosya_yolu"], '/');
    $fileName = time() . '_' . basename($_FILES['dosya']['name']);
    $targetFilePath = $dosya_yolu . '/' . $fileName;

    $dosya_yolu = $result["dosya_yolu"];
    $targetFilePathVT = $dosya_yolu . '/' . $fileName;

    try {
        // Upload to S3
        $result = $s3Client->putObject([
            'Bucket' => $config['s3']['bucket'],
            'Key'    => $targetFilePath,
            'SourceFile' => $_FILES['dosya']['tmp_name'],
        ]);

        // Prepare database insert
        $query = "INSERT INTO nokta_urunler_yuklemeler (
            aciklama, 
            aciklamaEn, 
            url_path, 
            yukleme_id, 
            is_active, 
            version,
            type, 
            datetime, 
            urun_id, 
            dosya_adi
        ) VALUES (
            :d1, :d2, :d3, :d4, :d5, :d6, :d7, :d8, :d9, :d10
        )";

        $params = [
            'd1' => $dosya_aciklama,
            'd2' => $dosya_aciklama_EN,
            'd3' => $targetFilePathVT,
            'd4' => $dosya_turu,
            'd5' => 1,
            'd6' => $dosya_versiyon,
            'd7' => $ext,
            'd8' => $dosya_tarihi,
            'd9' => $urun_id,
            'd10' => $fileName
        ];

        if ($database->insert($query, $params)) {
            header("Location: ../../pages/add-product.php?id=$urun_id");
            exit;
        } else {
            throw new Exception("Database insert failed");
        }

    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>