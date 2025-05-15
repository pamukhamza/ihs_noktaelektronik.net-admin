<?php
// Yapı Kredi test bilgileri
define("POS_URL", "https://entegrasyon.yapikredi.com.tr/3DSWebService/YKBPaymentService");
define("MERCHANT_ID", "XXXXXXXX"); // Mağaza numarası
define("TERMINAL_ID", "XXXXXX");   // Terminal numarası (6 hane)
define("POS_PASSWORD", "XXXXXX");  // Terminal şifresi (Hash yapılacak)
define("STORE_KEY", "XXXXXXX");    // Store key (3D secure hash için)

define("RETURN_URL", "https://www.siteniz.com/sonuc.php"); // 3D secure dönüş adresi
?>
