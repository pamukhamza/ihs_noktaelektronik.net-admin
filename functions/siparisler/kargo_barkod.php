<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once ('../db.php');
require_once('../../vendor/tcpdf/tcpdf.php');
require_once '../../vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$config = require_once '../../aws-config.php';

$s3Client = new S3Client([
    'version' => 'latest',
    'region'  => $config['s3']['region'],
    'credentials' => [
        'key'    => $config['s3']['key'],
        'secret' => $config['s3']['secret'],
    ]
]);

function uploadImageToS3Dekont($file_path, $upload_path, $s3Client, $bucket) {
    try {
        // S3 yükleme yolu
        $s3_file_path = $upload_path . basename($file_path); // Dosyanın basename'ini S3'e koyuyoruz

        // Dosyayı S3'e yükleyin
        $result = $s3Client->putObject([
            'Bucket' => $bucket,
            'Key'    => $s3_file_path,
            'SourceFile' => $file_path // SourceFile için dosya yolunu geçiyoruz
        ]);

        // Yükleme başarılı ise dosya adını veya URL'yi döndürüyoruz
        return basename($file_path); // veya $result['ObjectURL'] dönebilirsiniz, S3 URL'si için
    } catch (AwsException $e) {
        error_log('S3 yükleme hatası: ' . $e->getMessage());
        return false;
    }
}

$database = new Database();

function kargopdf($uye_id, $sip_id, $cargoKey)
{
    global $database, $s3Client, $config;

    $sip = $database->fetch("SELECT * FROM b2b_siparisler WHERE id = :sip_id", ['sip_id' => $sip_id]);
    $il_id = $sip["teslimat_il"];
    $ilce_id = $sip["teslimat_ilce"];
    $uyeAdSoyad = $sip["teslimat_ad"] . ' ' . $sip["teslimat_soyad"];
    $tel = $sip["teslimat_telefon"];
    $adres = $sip["teslimat_adres"];
    $firmaUnvani = $sip["teslimat_firmaadi"];

    $uye = $database->fetch("SELECT * FROM uyeler WHERE id = :uye_id", ['uye_id' => $uye_id]);

    $adressorgu = $database->fetch("SELECT * FROM b2b_adresler WHERE uye_id = :uye_id AND aktif = :aktif", ['uye_id' => $uye_id,'aktif' => '1']);

    $iller = $database->fetch("SELECT * FROM iller WHERE il_id = :il_id", ['il_id' => $il_id]);
    $il = $iller["il_adi"];

    $ilceler = $database->fetch("SELECT * FROM ilceler WHERE ilce_id = :ilce_id", ['ilce_id' => $ilce_id]);
    $ilce = $ilceler["ilce_adi"];

    // Create new PDF instance
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // Set document information
    $pdf->SetCreator('Nokta Elektronik ve Bilişim Sistemleri San. Tic. A.Ş.');
    $pdf->SetAuthor('');
    $pdf->SetTitle('Kargo');
    $pdf->SetSubject('Kargo');
    $pdf->SetKeywords('Kargo');
    // Set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    // Add a page
    $pdf->AddPage();
    // Set font
    $pdf->SetFont('dejavusans', '', 12);
    // Add company logo and name
    $pdf->Image('../../assets/images/logo_new.png', 10, 20, 40, '', 'PNG');
    $pdf->SetXY(10, 32); // Move to the right of the logo
    // Add title
    $pdf->SetFont('dejavusans', 'B', 16);
    // Draw a horizontal line
    $pdf->SetLineWidth(0.1);
    $pdf->Line(10, 50, $pdf->getPageWidth() - 10, 50);
    // Payer information and cargo details
    $pdf->SetFont('dejavusans', '', 12);
    $pdf->SetXY(10, 50); // Move down below the line
    $pdf->SetX(10);
    $pdf->Cell(20, 10, 'Alıcı', 0, 0, 'L');
    $pdf->Cell(0, 10, ': ' . (!empty($uyeAdSoyad) ? $uyeAdSoyad : $firmaUnvani), 0, 1, 'L');
    $pdf->SetX(10);
    $pdf->Cell(20, 10, 'Telefon', 0, 0, 'L');
    $pdf->Cell(0, 10, ': ' . $tel, 0, 1, 'L');
    $pdf->SetX(10);
    $pdf->Cell(20, 10, 'Adres', 0, 0, 'L');
    $pdf->Cell(0, 10, ': ' . $adres, 0, 1, 'L');
    $pdf->SetX(10);
    $pdf->Cell(20, 10, '', 0, 0, 'L');
    $pdf->Cell(0, 10, '  ' . $il . '/' . $ilce, 0, 1, 'L');
    $pdf->SetX(10);
    // Draw a horizontal line
    $pdf->SetLineWidth(0.1);
    $pdf->Line(10, 100, $pdf->getPageWidth() - 10, 100);
    // Add barcode
    $pdf->SetXY(10, 140); // Move down to a new section
    $style = array(
        'border' => 0,
        'vpadding' => 'auto',
        'hpadding' => 'auto',
        'fgcolor' => array(0, 0, 0),
        'bgcolor' => false, // array(255,255,255)
        'text' => true, // print the barcode text below the barcode
        'font' => 'helvetica', // barcode text font
        'fontsize' => 8, // barcode text font size in points
        'stretchtext' => 4 // stretch text
    );
    $pdf->write1DBarcode($cargoKey, 'C128', 40, 120, '', '', 1, $style, 'N');
    $pdf->Text(55, 115, 'Barkod No: ' . $cargoKey);
    $pdf->Text(55, 110, 'Web servis bilgi: 187205434');




    $kargo_adi = "kargo_" . uniqid() . ".pdf";
    $pdf_content = $pdf->Output('', 'S');
    $temp_file_path = sys_get_temp_dir() . '/' . uniqid('kargo_') . '.pdf';
    file_put_contents($temp_file_path, $pdf_content);

    $file_url = uploadImageToS3Dekont($temp_file_path, 'uploads/kargo/', $s3Client, $config['s3']['bucket']);
    if ($file_url) {
        $kargo_adi = basename($temp_file_path);
    }
    $database->insert("INSERT INTO b2b_kargo_pdf (sip_id, dosya) VALUES (:id, :dosya)", ['id' => $sip_id, 'dosya' => $kargo_adi]);

    unlink($temp_file_path);
}
?>