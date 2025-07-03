<?php
require '../../mail/mail_gonder.php'; // mailGonder ve diğer fonksiyonlar burada tanımlıysa

$alici = $_POST['alici'];
$konu = $_POST['konu'];
$icerik = $_POST['icerik'];
$baslik = $_POST['baslik'];

mailGonder($alici, $konu, $icerik, $baslik);
?>