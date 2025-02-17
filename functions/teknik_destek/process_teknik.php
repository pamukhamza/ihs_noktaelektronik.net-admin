<?php

include_once '../db.php';

include_once '../functions.php';
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

$database = new Database();


    $id = $_POST['id'];
    $yapilan_islemler = $_POST['yapilan_islemler'];
    $tekniker = $_POST['tekniker'];
    $seri_no = $_POST['seri_no'];
    $urun_durum = $_POST['urun_durum'];
    $teslim_tarih = $_POST['teslim_tarih'];
    $teslim_edilen = $_POST['teslim_edilen'];
    $tel = $_POST['tel'];


    // Array to store uploaded file names
    $fileNames = [];

    // Check if any files were uploaded
    if (!empty($_FILES['foto']['name'][0])) {
        $uploadedFiles = $_FILES['foto'];

        // Loop through uploaded files
        for ($i = 0; $i < count($uploadedFiles['name']); $i++) {
            $fileName = $uploadedFiles['name'][$i];
            $fileTmpName = $uploadedFiles['tmp_name'][$i];

            // Upload each image to S3
            $uploadedFileName = uploadImageToS3($_FILES['foto'], 'uploads/images/teknik-destek/', $s3Client, $config['s3']['bucket']);

            // If image uploaded successfully, add filename to $fileNames array
            if ($uploadedFileName) {
                $fileNames[] = $uploadedFileName;
            }
        }
    }

    // Update SQL query preparation and execution
    $query = "UPDATE teknik_destek_urunler SET tekniker = ?, yapilan_islemler = ?, foto = ?, seri_no = ?, urun_durumu = ?, teslim_edilen = ?, teslim_tarih = ? WHERE id = ?";
    $stmt = $db->prepare($query);

    // Join $fileNames array into comma-separated string
    $fotoString = implode(',', $fileNames);

    // Bind parameters and execute query
    $stmt->execute([$tekniker, $yapilan_islemler, $fotoString, $seri_no, $urun_durum, $teslim_edilen, $teslim_tarih, $id]);

    // Fetch the tdp_id after the update
    $query = "SELECT * FROM teknik_destek_urunler WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    $tdu = $stmt->fetch(PDO::FETCH_ASSOC);

    $tdp_id = $tdu["tdp_id"];
    $urun_kodu = $tdu["urun_kodu"];
    $urun_durum_id = $tdu["urun_durumu"];
    
    $query = "UPDATE nokta_teknik_destek SET tel = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$tel, $tdp_id]);

    $q = $db->prepare("SELECT * FROM nokta_teknik_durum WHERE id = ?");
    $q->execute([$urun_durum_id]);
    $utd = $q->fetch(PDO::FETCH_ASSOC);
    $urun_durumu = $utd["durum"];

    $q = $db->prepare("SELECT * FROM nokta_teknik_destek WHERE id = ?");
    $q->execute([$tdp_id]);
    $ntd = $q->fetch(PDO::FETCH_ASSOC);
    $musteri = $ntd["musteri"];
    $mail = $ntd["mail"];
    $takip_no = $ntd["takip_kodu"];


    if($urun_durum_id != 1 && $urun_durum_id != 2 && $urun_durum_id != 3){
        $mail_icerik = islemiBitenAriza($musteri, $takip_no, $urun_durumu, $urun_kodu);
        mailGonder($mail, 'Arızalı Cihaz Durumu!', $mail_icerik, 'Nokta Elektronik');
    }

    return $tdp_id;

?>