<?php
require '../../mail/mail_gonder.php'; // mailGonder ve diğer fonksiyonlar burada tanımlıysa

//$alici = $_POST['alici'];
//$konu = $_POST['konu'];
//$icerik = $_POST['icerik'];
//$baslik = $_POST['baslik'];

//mailGonder($alici, $konu, $icerik, $baslik);
mailGonder('hmzpmk34@gmail.com', 'deneme konusu', 'yok', 'baslik');
echo "geldi";
?>