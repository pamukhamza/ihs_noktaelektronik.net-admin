<?php
include '../../db.php';
$database = new Database();
// Fotoğrafın yolu
$imagePath = 'bos_sertifika.jpg'; // Fotoğrafın yolu
$outputPath = 'output_image.jpg'; // Çıktı fotoğrafının yolu

// Gelen verileri al
$tarih = date('d/m/Y');
$uye_id = $_POST['uye_id'];
$field1 = $_POST['orta_yazi'];
$yazi1 =  $field1 . ' yapısal kablolama projesinde uçtan uca kullanılan ve sonlandırılan tüm "OringNetworking" kablo ve ekipmanları bu sertifika ile "25 Yıl" boyunca garanti altına alınmıştır.';
$yazi2 = 'İşbu sertifika ' . $tarih . ' tarihinde, ' . $field1 . ' yapısal kablolama projesinde uçtan uca kullanılan ve sonlandırılan tüm OringNetworking markalı ürünler için düzenlenmiştir.';

// Fotoğrafı aç
$image = imagecreatefromjpeg($imagePath);

// Renk ayarla (siyah)
$black = imagecolorallocate($image, 0, 0, 0);

// Yazı fontu ve boyutu
$fontPath = '../../../assets/fonts/roboto/Roboto-Light.ttf';
$boldItalicFontPath = '../../../assets/fonts/roboto/Roboto-BoldItalic.ttf'; // Yeni font eklendi
$fontSize = 60;
$fontSize2 = 30;

// Yazıyı yerleştir (satır haline getirme)
function addMultilineText($image, $text, $x, $y, $fontPath, $boldItalicFontPath, $fontSize, $color, $maxWidth) {
    $words = explode(' ', $text);
    $line = '';
    $lineY = $y;

    foreach ($words as $word) {
        $currentFontPath = (in_array($word, ['"OringNetworking"', '"25', 'Yıl"'])) ? $boldItalicFontPath : $fontPath;
        $testLine = $line . $word . ' ';
        $testBox = imagettfbbox($fontSize, 0, $currentFontPath, $testLine);
        $testWidth = $testBox[2] - $testBox[0];

        if ($testWidth > $maxWidth && $line !== '') {
            drawFormattedLine($image, $line, $x, $lineY, $fontPath, $boldItalicFontPath, $fontSize, $color, $maxWidth);
            $line = $word . ' ';
            $lineY += 100;
        } else {
            $line = $testLine;
        }
    }

    if ($line !== '') {
        drawFormattedLine($image, $line, $x, $lineY, $fontPath, $boldItalicFontPath, $fontSize, $color, $maxWidth);
    }
}

function addMultilineTextSimple($image, $text, $x, $y, $fontPath, $boldItalicFontPath, $fontSize, $color, $maxWidth) {
    $words = explode(' ', $text);
    $line = '';
    $lineY = $y;

    foreach ($words as $word) {
        $currentFontPath = preg_match('/\d{2}\/\d{2}\/\d{4}/', $word) ? $boldItalicFontPath : $fontPath;
        $testLine = $line . $word . ' ';
        $testBox = imagettfbbox($fontSize, 0, $currentFontPath, $testLine);
        $testWidth = $testBox[2] - $testBox[0];

        if ($testWidth > $maxWidth && $line !== '') {
            drawFormattedLineSimple($image, $line, $x, $lineY, $fontPath, $boldItalicFontPath, $fontSize, $color, $maxWidth);
            $line = $word . ' ';
            $lineY += 50;
        } else {
            $line = $testLine;
        }
    }

    if ($line !== '') {
        drawFormattedLineSimple($image, $line, $x, $lineY, $fontPath, $boldItalicFontPath, $fontSize, $color, $maxWidth);
    }
}

function drawFormattedLine($image, $line, $x, $y, $fontPath, $boldItalicFontPath, $fontSize, $color, $maxWidth) {
    $words = explode(' ', trim($line));
    $lineWidth = 0;
    foreach ($words as $word) {
        $currentFontPath = (in_array($word, ['"OringNetworking"', '"25', 'Yıl"'])) ? $boldItalicFontPath : $fontPath;
        $bbox = imagettfbbox($fontSize, 0, $currentFontPath, $word . ' ');
        $lineWidth += $bbox[2] - $bbox[0];
    }

    $x_offset = $x + ($maxWidth - $lineWidth) / 2; // Center the line

    foreach ($words as $word) {
        $currentFontPath = (in_array($word, ['"OringNetworking"', '"25', 'Yıl"'])) ? $boldItalicFontPath : $fontPath;
        $bbox = imagettfbbox($fontSize, 0, $currentFontPath, $word . ' ');
        imagettftext($image, $fontSize, 0, (int)$x_offset, (int)$y, $color, $currentFontPath, $word . ' ');
        $x_offset += $bbox[2] - $bbox[0];
    }
}

function drawFormattedLineSimple($image, $line, $x, $y, $fontPath, $boldItalicFontPath, $fontSize, $color, $maxWidth) {
    $words = explode(' ', trim($line));
    $lineWidth = 0;
    foreach ($words as $word) {
        $currentFontPath = preg_match('/\d{2}\/\d{2}\/\d{4}/', $word) ? $boldItalicFontPath : $fontPath;
        $bbox = imagettfbbox($fontSize, 0, $currentFontPath, $word . ' ');
        $lineWidth += $bbox[2] - $bbox[0];
    }

    $x_offset = $x + ($maxWidth - $lineWidth) / 2; // Center the line

    foreach ($words as $word) {
        $currentFontPath = preg_match('/\d{2}\/\d{2}\/\d{4}/', $word) ? $boldItalicFontPath : $fontPath;
        $bbox = imagettfbbox($fontSize, 0, $currentFontPath, $word . ' ');
        imagettftext($image, $fontSize, 0, (int)$x_offset, (int)$y, $color, $currentFontPath, $word . ' ');
        $x_offset += $bbox[2] - $bbox[0];
    }
}

// Yazıyı ekle
$maxWidth = 2700; // Yazı için maksimum genişlik
addMultilineText($image, $yazi1, 625, 1200, $fontPath, $boldItalicFontPath, $fontSize, $black, $maxWidth); // Orta yazı
addMultilineTextSimple($image, $yazi2, 625, 2350, $fontPath, $boldItalicFontPath, $fontSize2, $black, $maxWidth); // Alt yazı

// Fotoğrafı kaydet
imagejpeg($image, $outputPath);

// Belleği temizle
imagedestroy($image);

// FPDF kütüphanesini dahil et
require('../../../assets/fpdf/fpdf.php');

// Yeni bir PDF oluştur
$pdf = new FPDF('L', 'mm', array(297, 210)); // A4 yatay
$pdf->AddPage();

// JPG'yi PDF'e ekle
$pdf->Image($outputPath, 0, 0, 297, 210);

// PDF'i kaydet
$pdfPath = 'output.pdf';
$pdf->Output('F', $pdfPath);

// PDF'i indir
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . basename($pdfPath) . '"');
readfile($pdfPath);

// Geçici dosyaları temizle
unlink($outputPath);
unlink($pdfPath);

$query = "INSERT INTO b2b_sertifikalar (field1, uye_id) VALUES (:field1, :uye_id)";
$params = [
    'field1' => $field1,
    'uye_id' => $uye_id
];
$database->insert($query, $params);

?>