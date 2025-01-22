<?php
// Veritabanı bağlantısı
try {
    $dbB = new PDO("mysql:host=localhost;dbname=nktdnm;charset=utf8", "root", "");
    $dbB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanına bağlanılamadı: " . $e->getMessage());
}

try {
    // A veritabanından verileri al
    $queryA = "SELECT id, UrunKodu FROM nokta_urunler";
    $stmtA = $dbB->query($queryA);
    $productsA = $stmtA->fetchAll(PDO::FETCH_ASSOC);

    // Her bir kayıt için kontrol yap
    foreach ($productsA as $product) {
        $urunKodu = $product['UrunKodu'];

        // B veritabanında UrunKodu ile eşleşme kontrolü
        $queryB = "SELECT COUNT(*) FROM nokta_urunler_net WHERE UrunKodu = :urunKodu";
        $stmtB = $dbB->prepare($queryB);
        $stmtB->execute([':urunKodu' => $urunKodu]);
        $exists = $stmtB->fetchColumn();

        if ($exists) {
            $updateQuery = "UPDATE nokta_urunler_net SET id = :BLKODU WHERE UrunKodu = :UrunKodu";

            $stmtUpdate = $dbB->prepare($updateQuery);
            $stmtUpdate->execute([
                ':BLKODU' => $product['id'],
                ':UrunKodu' => $urunKodu
            ]);
            echo "Güncellendi: UrunKodu - $urunKodu\n";
        }
    }

    echo "Veri senkronizasyonu tamamlandı.";
} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
}