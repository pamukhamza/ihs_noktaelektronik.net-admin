<?php
// mail_gonder.php dosyasının doğru yolda olduğundan emin olun.
// Bu betik kök dizinde çalıştırıldığı için yol 'mail/mail_gonder.php' olmalı.
require_once 'mail/mail_gonder.php';
require_once 'functions/db.php'; // mailGonder içinde veritabanı kullanılıyorsa ekleyin
require_once 'functions/functions.php'; // mailGonder içinde başka fonksiyonlar kullanılıyorsa ekleyin

// Komut satırı argümanlarını kontrol et
if (count($argv) < 5) {
    // Hata loglama veya basit bir çıkış
    error_log("mail_worker.php: Yetersiz parametre."); 
    exit("Yetersiz parametre");
}

$to = $argv[1];
$subject = $argv[2];
$message = $argv[3];
$from = $argv[4];

// Mail gönderme fonksiyonunu çağır
mailGonder($to, $subject, $message, $from);

// Başarılı olursa loglama yapabilirsiniz (isteğe bağlı)
// error_log("Mail başarıyla gönderildi: " . $to); 

?> 