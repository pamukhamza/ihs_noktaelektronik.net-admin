<?php
// Gerekli dosyaları doğru yollarla include edelim
$rootPath = dirname(__FILE__);
require_once $rootPath . '/mail/mail_gonder.php';
require_once $rootPath . '/functions/db.php';
require_once $rootPath . '/functions/functions.php';

// Hata raporlamayı aktif edelim
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log dosyası oluşturalım
$logFile = $rootPath . '/mail_worker.log';

function writeLog($message) {
    global $logFile;
    $date = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$date] $message\n", FILE_APPEND);
}

// Komut satırı argümanlarını kontrol edelim
if (count($argv) < 5) {
    writeLog("Yetersiz parametre: " . print_r($argv, true));
    exit("Yetersiz parametre");
}

try {
    $to = $argv[1];
    $subject = $argv[2];
    $message = $argv[3];
    $from = $argv[4];

    writeLog("Mail gönderme başladı - Alıcı: $to, Konu: $subject");
    
    // Mail gönderme fonksiyonunu çağır
    mailGonder($to, $subject, $message, $from);
    
    writeLog("Mail başarıyla gönderildi");
} catch (Exception $e) {
    writeLog("Hata oluştu: " . $e->getMessage());
    exit("Hata: " . $e->getMessage());
}
?> 