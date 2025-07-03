<?php
file_put_contents(__DIR__ . '/mail_debug.log', "Başladı\n", FILE_APPEND);
parse_str(file_get_contents("php://input"), $post);
file_put_contents(__DIR__ . '/mail_debug.log', print_r($post, true), FILE_APPEND);
require '../../mail/mail_gonder.php'; // mailGonder ve diğer fonksiyonlar burada tanımlıysa

$alici = $_POST['alici'];
$konu = $_POST['konu'];
$icerik = $_POST['icerik'];
$baslik = $_POST['baslik'];

mailGonder($alici, $konu, $icerik, $baslik);
file_put_contents(__DIR__ . '/mail_debug.log', "Mail gönderildi\n", FILE_APPEND);
?>