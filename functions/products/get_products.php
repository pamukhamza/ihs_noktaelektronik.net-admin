<?php
include_once '../db.php';

$database = new Database();

// Simple query to get only necessary data
$query = "
    SELECT 
        p.id,
        p.UrunKodu,
        p.UrunAdiTR,
        COALESCE(m.title, '') as title,
        COALESCE(c.KategoriAdiTR, 'Kategori Yok') as category_name,
        p.Vitrin,
        p.YeniUrun,
        p.aktif
    FROM nokta_urunler p 
    LEFT JOIN nokta_kategoriler c ON p.KategoriID = c.id
    LEFT JOIN nokta_urun_markalar AS m ON m.id = p.MarkaID
";

$results = $database->fetchAll($query);

// Return JSON response
header('Content-Type: application/json');
echo json_encode(['data' => $results]);