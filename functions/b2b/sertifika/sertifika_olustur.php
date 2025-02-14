<?php
include '../../db.php';
$database = new Database();

// Fotoğrafın yolu
$imagePath = 'https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/sertifika/bos_sertifika.jpg'; // Fotoğrafın yolu

// Gelen verileri al
$tarih = date('d/m/Y');
$uye_id = $_POST['uye_id'];
$field1 = $_POST['orta_yazi'];
$yazi1 =  $field1 . ' yapısal kablolama projesinde uçtan uca kullanılan ve sonlandırılan tüm "OringNetworking" kablo ve ekipmanları bu sertifika ile "25 Yıl" boyunca garanti altına alınmıştır.';
$yazi2 = 'İşbu sertifika ' . $tarih . ' tarihinde, ' . $field1 . ' yapısal kablolama projesinde uçtan uca kullanılan ve sonlandırılan tüm OringNetworking markalı ürünler için düzenlenmiştir.';

// Debugging information
error_log("Received data - uye_id: $uye_id, field1: $field1, tarih: $tarih");

// Check if the GD library is installed
if (!extension_loaded('gd')) {
    error_log("GD library is not installed");
    die("GD library is not installed");
}

// Check if the image file is accessible
$imageData = file_get_contents($imagePath);
if ($imageData === false) {
    error_log("Failed to access image at $imagePath");
    die("Failed to access image at $imagePath");
}

// Create image from string
$image = imagecreatefromstring($imageData);
if (!$image) {
    error_log("Failed to create image from $imagePath");
    die("Failed to create image from $imagePath");
}

// Debugging information
error_log("Image created successfully from $imagePath");

// Renk ayarla (siyah)
$black = imagecolorallocate($image, 0, 0, 0);

// Yazı fontu ve boyutu
$fontPath = '../../../assets/fonts/roboto/Roboto-Light.ttf';
$boldItalicFontPath = '../../../assets/fonts/roboto/Roboto-BoldItalic.ttf'; // Yeni font eklendi
$fontSize = 60;
$fontSize2 = 30;

// Debugging information
error_log("Font paths - fontPath: $fontPath, boldItalicFontPath: $boldItalicFontPath");

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

// Set headers to download the image
header('Content-Type: image/jpeg');
header('Content-Disposition: attachment; filename="output_image.jpg"');

// Output the image directly to the browser
imagejpeg($image);

// Debugging information
error_log("Image output to browser");

// Belleği temizle
imagedestroy($image);

// Veritabanına kayıt ekle
$query = "INSERT INTO b2b_sertifikalar (field1, uye_id) VALUES (:field1, :uye_id)";
$params = [
    'field1' => $field1,
    'uye_id' => $uye_id
];
$database->insert($query, $params);

// Debugging information
error_log("Database record inserted - field1: $field1, uye_id: $uye_id");

?>