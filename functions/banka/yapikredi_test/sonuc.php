<?php
$response = $_POST; // Yapı Kredi’den dönen veriler burada olur

if ($response["Response"] == "Approved") {
    echo "Ödeme başarılı. Sipariş numarası: " . $response["OrderId"];
    // Burada veritabanına kayıt yapabilirsiniz
} else {
    echo "Ödeme başarısız. Hata: " . $response["ErrMsg"];
}
?>
