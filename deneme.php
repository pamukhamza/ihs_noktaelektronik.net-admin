<?php
$host = 'localhost';
$dbname = 'nokta';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
function fetchContentWithCurl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.9',
        'Cache-Control: no-cache'
    ]);
    curl_setopt($ch, CURLOPT_REFERER, 'https://www.google.com/'); // Referer ekle
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return false;
    }
    return $html;
}

$url = 'https://www.dahuasecurity.com/ceen/products/All-Products/Network-Cameras/Lite-Series/2-MP/IPC-HDBW2231F-AS-S2=S2';
$html = fetchContentWithCurl($url);

if (!$html) {
    die("Sayfa içerik alınamadı. HTTP hatası olabilir.");
}

// DOMDocument ile HTML içeriğini analiz et
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_clear_errors();

$xpath = new DOMXPath($dom);
$elements = $xpath->query('//*[@id="table001"]');

if ($elements->length === 0) {
    die("Belirtilen ID'ye sahip içerik bulunamadı.");
}

// Verileri veritabanına kaydet
foreach ($elements as $element) {
    // HTML etiketleri ve özellikleriyle birlikte al
    $content = $dom->saveHTML($element);
    
    // Veriyi veritabanına ekle
    $stmt = $db->prepare("INSERT INTO hedef_tablo (icerik) VALUES (:icerik)");
    $stmt->execute(['icerik' => $content]);
}

echo "Veri başarıyla çekildi ve kaydedildi.";
