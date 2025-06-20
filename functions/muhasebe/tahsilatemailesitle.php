<?php
require_once '../db.php'; // PDO tabanlı veritabanı sınıfını dahil et

$db = new Database(); // Database sınıfı örneği oluştur

if (isset($_POST['emailesitle'])) {
    try {
        $query = "
            UPDATE vadesi_gecmis_borc v
            JOIN tahsilat_email t ON v.bilgi_kodu = t.BLKODU
            SET v.email = t.email
        ";
        
        $stmt = $db->update($query); // Database sınıfındaki update fonksiyonu kullanılabilir

        if ($stmt) {
            echo "✅ E-mail bilgileri başarıyla eşitlendi.";
            echo "<br>";
            echo "<a href='../../pages/b2b/b2b-vadesi-gecmis?w=noktab2b'>Geri Dön</a>";
        } else {
            echo "❌ Eşitleme sırasında bir hata oluştu.";
        }

    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
} else {
    echo "Geçersiz istek.";
}
?>
