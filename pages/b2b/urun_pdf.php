<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
try {
    require_once('../../vendor/autoload.php'); // veya tcpdf klasörünüzün yolunu verin
    require_once '../../vendor/tcpdf/tcpdf.php';
    require_once '../../functions/db.php'; // PDO bağlantınız bu dosyada olmalı

    // TCPDF başlat
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistem');
    $pdf->SetTitle('Ürün Listesi');
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    // Başlık
    $pdf->SetFont('dejavusans', 'B', 14);
    $pdf->Cell(0, 10, 'Ürün Listesi', 0, 1, 'C');
    $pdf->Ln(5);

    // Tablo başlıkları
    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->Cell(60, 10, 'Stok Kodu', 1, 0, 'L');
    $pdf->Cell(80, 10, 'Ürün Adı', 1, 0, 'L');
    $pdf->Cell(40, 10, 'Fiyat', 1, 1, 'L');

    // Veritabanı sorgusu
    $db = new Database();
    $products = $db->fetchAll("SELECT UrunKodu, UrunAdiTR, KSF4, DSF4, DOVIZ_BIRIMI FROM nokta_urunler");

    $pdf->SetFont('dejavusans', '', 9);

    // Satırları yaz
    foreach ($products as $product) {
        $urunKodu = $product['UrunKodu'];
        $urunAdi  = $product['UrunAdiTR'];
        $ksf4     = $product['KSF4'];
        $dsf4     = $product['DSF4'];
        $doviz    = $product['DOVIZ_BIRIMI'];

        if (!empty($ksf4)) {
            $fiyat = number_format($ksf4, 2, ',', '.') . ' ₺';
        } else {
            $fiyat = number_format($dsf4, 2, ',', '.') . ' ' . $doviz;
        }

        $pdf->Cell(60, 8, $urunKodu, 1, 0, 'L');
        $pdf->Cell(80, 8, $urunAdi, 1, 0, 'L');
        $pdf->Cell(40, 8, $fiyat, 1, 1, 'L');
    }

    // PDF çıktısı
    $pdf->Output('urun_listesi.pdf', 'I'); // veya 'D' ile indir
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage();
}