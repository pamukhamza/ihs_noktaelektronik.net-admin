<?php
include_once '../../db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize input
    $database = new Database();
    $query = "SELECT nef.*,nu.id,nu.UrunAdiTr FROM net_offers AS nef WHERE nef.id = $id
    LEFT JOIN nokta_urunler AS nu ON nef.prod_id = nu.id
    ";
    $result = $database->fetch($query);

    if ($result) {
        // Output the details
        echo '<p><strong>Ad:</strong> ' . htmlspecialchars($result['name']) . '</p>';
        echo '<p><strong>Firma:</strong> ' . htmlspecialchars($result['company']) . '</p>';
        echo '<p><strong>Telefon:</strong> ' . htmlspecialchars($result['phone']) . '</p>';
        echo '<p><strong>E-Posta:</strong> ' . htmlspecialchars($result['mail']) . '</p>';
        echo '<p><strong>Tarih:</strong> ' . htmlspecialchars($result['date']) . '</p>';
        echo '<p><strong>Tarih:</strong> ' . htmlspecialchars($result['UrunAdiTr']) . '</p>';
        echo '<p><strong>Açıklama:</strong> ' . htmlspecialchars($result['description']) . '</p>';
    } else {
        echo '<p>No data found for this ID.</p>';
    }
} else {
    echo '<p>Invalid request.</p>';
}
?>
