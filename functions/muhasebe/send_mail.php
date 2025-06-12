<?php
// Hata raporlamayı aktifleştir
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../db.php';
include_once '../../mail/tahsilat_mail_gonder.php'; // mailGonder ve vadeGecikmeHatirlatma fonksiyonları burada tanımlı olmalı

header('Content-Type: application/json');

// Hata yakalama fonksiyonu
function handleError($errno, $errstr, $errfile, $errline) {
    error_log("PHP Error [$errno] $errstr on line $errline in file $errfile");
    echo json_encode([
        'success' => false,
        'message' => "PHP Error: $errstr",
        'debug' => [
            'file' => $errfile,
            'line' => $errline
        ]
    ]);
    exit;
}

// Fatal error yakalama
function handleFatalError() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        error_log("Fatal Error: " . $error['message']);
        echo json_encode([
            'success' => false,
            'message' => "Fatal Error: " . $error['message'],
            'debug' => [
                'file' => $error['file'],
                'line' => $error['line']
            ]
        ]);
        exit;
    }
}

set_error_handler('handleError');
register_shutdown_function('handleFatalError');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database();
        
        $id = $_POST['id'] ?? null;

        if (!$id) {
            throw new Exception('ID parametresi eksik');
        }

        $sql = "SELECT * FROM vadesi_gecmis_borc WHERE id = :id";
        $params = ['id' => $id];
        $borc = $database->fetch($sql, $params);

        if (!$borc) {
            throw new Exception('Borç kaydı bulunamadı');
        }

        if (empty($borc['email'])) {
            throw new Exception('Müşteri e-posta adresi bulunamadı');
        }

        // 🔐 Şifrelenecek veriler
        $veri = [
            'cari_kodu'      => $borc['cari_kodu'],
            'ticari_unvani'  => $borc['ticari_unvani'],
            'geciken_tutar'  => $borc['geciken_tutar'],
            'borc_bakiye'    => $borc['borc_bakiye'],
            'bilgi_kodu'     => $borc['bilgi_kodu']
        ];

        // 🔁 JSON + base64 encode
        $sifreli = base64_encode(json_encode($veri));

        // 📝 odeme_link alanını güncelle
        $updateSql = "UPDATE vadesi_gecmis_borc SET odeme_link = :odeme_link WHERE id = :id";
        $updateResult = $database->insert($updateSql, ['odeme_link' => $sifreli, 'id' => $id]);

        if (!$updateResult) {
            throw new Exception('Ödeme linki güncellenirken hata oluştu');
        }

        // 🔗 Link adresi
        $odemeUrl = "https://www.siteniz.com/odeme.php?data=" . urlencode($sifreli);

        // 📧 Mail içeriği
        $subject = "Vadesi Geçmiş Borç Hatırlatması";
        $mailContent = vadeGecikmeHatirlatma($borc, $odemeUrl);
        $mailBaslik = "Nokta Net Tahsilat";
        $aliciMail = $borc['email'];

        // ✉️ Mail gönder
        $mailResult = mailGonder($aliciMail, $subject, $mailContent, $mailBaslik);

        if (!$mailResult) {
            throw new Exception('E-posta gönderilemedi');
        }

        echo json_encode(['success' => true]);
        exit;

    } catch (Exception $e) {
        error_log("Send Mail Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

echo json_encode([
    'success' => false,
    'message' => 'Geçersiz istek metodu'
]);
