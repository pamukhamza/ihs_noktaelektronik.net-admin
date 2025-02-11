<?php 

include_once '../../db.php';
$database = new Database();
require '../../../vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$config = require '../../../aws-config.php';

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

    $sId = $_POST['id'];
    $sLink = $_POST['popupLink'];
    $sGorsel = $_FILES['popupGorsel']['name'];
    $aktif = 0;
    // Establishing PDO connection
    global $db;
    if ($sId) {
            $query = "UPDATE b2b_popup_kampanya SET `link` = :link" . (!empty($sGorsel) ? ", `foto` = :foto" : "") .  "WHERE id = :id";
            $params = [
                'link' => $sLink,
                'id' => $sId
            ];

            if (!empty($sGorsel)) {
                try {
                    $fileName = time() . '_' . basename($_FILES['popupGorsel']['name']);
                    $targetFilePath = 'uploads/images/campaigns/' . $fileName;
    
                    $result = $s3Client->putObject([
                        'Bucket' => $config['s3']['bucket'],
                        'Key'    => $targetFilePath,
                        'SourceFile' => $_FILES['popupGorsel']['tmp_name'],
                    ]);
    
                    $params['foto'] = $fileName;
                } catch (AwsException $e) {
                    echo "Error uploading file: " . $e->getMessage();
                    exit;
                }
            }
    
            if ($database->update($query, $params)) {
                echo "Kayıt başarıyla eklendi.";
            } else {
                echo "Ekleme sırasında hata oluştu.";
            }
      
    } else {
        $query = "INSERT INTO b2b_popup_kampanya (link, aktif" . (!empty($sGorsel) ? ", `foto`" : "") .") VALUES (:link, :aktif" . (!empty($sGorsel) ? ", :foto" : "") .")";
        $params = [
            'link' => $sLink,
            'aktif' => $aktif
        ];

        if (!empty($sGorsel)) {
            try {
                $fileName = time() . '_' . basename($_FILES['popupGorsel']['name']);
                $targetFilePath = 'uploads/images/campaigns/' . $fileName;

                $result = $s3Client->putObject([
                    'Bucket' => $config['s3']['bucket'],
                    'Key'    => $targetFilePath,
                    'SourceFile' => $_FILES['popupGorsel']['tmp_name'],
                ]);

                $params['foto'] = $fileName;
            } catch (AwsException $e) {
                echo "Error uploading file: " . $e->getMessage();
                exit;
            }
        }

        if ($database->insert($query, $params)) {
            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }
    }


?>