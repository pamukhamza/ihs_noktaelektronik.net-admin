<?php 
$start = microtime(true);
for ($i = 0; $i < 10; $i++) {
    $db = new PDO("mysql:host=127.0.0.1;dbname=noktanetdb;charset=utf8mb4", "nokt_admin", "Dell28736.!");
}
echo "Toplam 10 bağlantı süresi: " . round((microtime(true) - $start), 3) . " saniye";
?>