<?php
ini_set('memory_limit', '-1'); // Bellek sınırı tamamen kaldırılır

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

try {
    // Veritabanına bağlanma
    $db = new PDO("mysql:host=localhost;dbname=nokta;charset=utf8", "root", "");

    // BLKODU = 0 veya boş olan ürünleri çekme
    $query = $db->prepare("
        SELECT id, UrunAdiTR, UrunKodu, KategoriID 
        FROM nokta_urunler 
        WHERE BLKODU = 0 OR BLKODU IS NULL OR TRIM(BLKODU) = ''
    ");
    $query->execute();
    $products = $query->fetchAll(PDO::FETCH_ASSOC);

    if (count($products) > 0) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Excel başlıklarını ayarla
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Ürün Adı');
        $sheet->setCellValue('C1', 'Ürün Kodu');
        $sheet->setCellValue('D1', 'Kategori Hiyerarşisi');

        // Kategori hiyerarşisini çözmek için yardımcı bir fonksiyon
        function getCategoryHierarchy($db, $kategoriID) {
            $hierarchy = [];
            while ($kategoriID) {
                $query = $db->prepare("SELECT id, parent_id, KategoriAdiTr FROM nokta_kategoriler WHERE id = ?");
                $query->execute([$kategoriID]);
                $category = $query->fetch(PDO::FETCH_ASSOC);

                if ($category) {
                    $hierarchy[] = $category['KategoriAdiTr']; // Kategori adını hiyerarşiye ekle
                    $kategoriID = $category['parent_id']; // Bir üst parent_id'ye geç
                } else {
                    break;
                }
            }
            return implode(' > ', array_reverse($hierarchy)); // Hiyerarşiyi düzelt ve döndür
        }

        // Verileri Excel'e yazma
        $row = 2; // Veriler A2 hücresinden başlayacak
        foreach ($products as $product) {
            $sheet->setCellValue('A' . $row, $product['id']);
            $sheet->setCellValue('B' . $row, $product['UrunAdiTR']);
            $sheet->setCellValue('C' . $row, $product['UrunKodu']);
            $sheet->setCellValue('D' . $row, getCategoryHierarchy($db, $product['KategoriID']));
            $row++;
        }

        // Excel dosyasını kaydet
        $writer = new Xlsx($spreadsheet);
        $fileName = 'urunler_kategori_hiyerarsi.xlsx';
        $writer->save($fileName);

        echo "Ürünler başarıyla '{$fileName}' dosyasına aktarıldı.";
    } else {
        echo "BLKODU = 0 veya boş olan ürün bulunamadı.";
    }
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
} catch (Exception $e) {
    die("Bir hata oluştu: " . $e->getMessage());
}
?>
