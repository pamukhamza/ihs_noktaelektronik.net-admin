<?php
include_once '../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    
    $id = $_POST['id'] ?? null;
    $email = $_POST['email'] ?? null;
    
    if ($id && $email) {
        // Borç bilgilerini al
        $sql = "SELECT * FROM vadesi_gecmis_borc WHERE id = :id";
        $params = ['id' => $id];
        $borc = $database->fetch($sql, $params);
        
        if ($borc) {
            // Mail içeriğini hazırla
            $subject = "Vadesi Geçmiş Borç Hatırlatması";
            $message = "Sayın {$borc['ticari_unvani']},\n\n";
            $message .= "Vadesi geçmiş borcunuz bulunmaktadır.\n";
            $message .= "Borç Tutarı: " . number_format($borc['geciken_tutar'], 2, ',', '.') . " ₺\n";
            $message .= "Gerçek Vade: {$borc['gerc_vade']}\n\n";
            $message .= "Lütfen en kısa sürede ödeme yapmanızı rica ederiz.\n\n";
            $message .= "Saygılarımızla,\nNokta Net";
            
            $headers = "From: noreply@noktanet.com\r\n";
            $headers .= "Reply-To: info@noktanet.com\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            
            // Maili gönder
            if (mail($email, $subject, $message, $headers)) {
                echo json_encode(['success' => true]);
                exit;
            }
        }
    }
}

echo json_encode(['success' => false]); 