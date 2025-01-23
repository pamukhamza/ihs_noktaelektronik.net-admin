<?php
ini_set('memory_limit', '-1'); // Bellek sınırı tamamen kaldırılır

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

try {
    // Veritabanına bağlanma
    $db = new PDO("mysql:host=localhost;dbname=nokta;charset=utf8", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL JOIN ile kategori hiyerarşisini sorgula
    $query = $db->prepare("
        SELECT 
            u.id, 
            u.UrunAdiTR, 
            u.UrunKodu, 
            COALESCE(
                CONCAT_WS(' > ', 
                    c5.KategoriAdiTr, 
                    c4.KategoriAdiTr, 
                    c3.KategoriAdiTr, 
                    c2.KategoriAdiTr, 
                    c1.KategoriAdiTr
                ), 
                'Kategori Bulunamadı'
            ) AS KategoriHiyerarsi
        FROM nokta_urunler u
        LEFT JOIN nokta_kategoriler c1 ON u.KategoriID = c1.id
        LEFT JOIN nokta_kategoriler c2 ON c1.parent_id = c2.id
        LEFT JOIN nokta_kategoriler c3 ON c2.parent_id = c3.id
        LEFT JOIN nokta_kategoriler c4 ON c3.parent_id = c4.id
        LEFT JOIN nokta_kategoriler c5 ON c4.parent_id = c5.id
        WHERE u.BLKODU = 0 OR u.BLKODU IS NULL OR TRIM(u.BLKODU) = ''
    ");
    $query->execute();

    // Excel işlemleri
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Excel başlıklarını ayarla
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Ürün Adı');
    $sheet->setCellValue('C1', 'Ürün Kodu');
    $sheet->setCellValue('D1', 'Kategori Hiyerarşisi');

    // Verileri Excel'e yazma
    $row = 2; // Veriler A2 hücresinden başlayacak
    while ($product = $query->fetch(PDO::FETCH_ASSOC)) {
        $sheet->setCellValue('A' . $row, $product['id']);
        $sheet->setCellValue('B' . $row, $product['UrunAdiTR']);
        $sheet->setCellValue('C' . $row, $product['UrunKodu']);
        $sheet->setCellValue('D' . $row, $product['KategoriHiyerarsi']);
        $row++;
    }

    // Excel dosyasını kaydet
    $writer = new Xlsx($spreadsheet);
    $fileName = 'urunler_kategori_hiyerarsi123.xlsx';
    $writer->save($fileName);

    echo "Ürünler başarıyla '{$fileName}' dosyasına aktarıldı.";
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
} catch (Exception $e) {
    die("Bir hata oluştu: " . $e->getMessage());
}
