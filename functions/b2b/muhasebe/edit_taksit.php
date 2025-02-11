<?php
include_once '../../db.php';
function taksit() {
    try {
        $database = new Database();
        $id = $_POST['id'];
        $kart_id = $_POST['kart_id'];
        $taksit = $_POST['taksit'];
        $vade = $_POST['vade'];
        $aciklama = $_POST['aciklama'];
        $yonlendirme = $_POST['yonlendirme'];
        $programeslestirme = $_POST['programeslestirme'];
        $aktif = 1;

        if ($id) {
            $query = "UPDATE b2b_banka_taksit_eslesme SET taksit = :taksit, vade = :vade, aciklama = :aciklama, pos_id = :pos_id, ticari_program = :ticari WHERE id = :id";
            $var = $database->update($query, array('taksit' => $taksit, 'vade' => $vade, 'aciklama' => $aciklama ,'pos_id' => $yonlendirme , 'ticari' => $programeslestirme, 'id' => $id));
        } else { 
            $query = "INSERT INTO b2b_banka_taksit_eslesme (kart_id, taksit, vade, aciklama, pos_id, aktif, ticari_program) VALUES (:kart_id, :taksit, :vade, :aciklama, :pos_id, :aktif, :ticari_program)";
            $var = $database->insert($query, array('kart_id' => $kart_id, 'taksit' => $taksit, 'vade' => $vade, 'aciklama' => $aciklama ,'pos_id' => $yonlendirme , 'aktif' => $aktif , 'ticari_program' => $programeslestirme ));
        }
    } catch (Exception $e) {
        // Hata mesajını JSON formatında döndür
        echo json_encode(["error" => "Bir hata oluştu: " . $e->getMessage()]);
    }
}
if (isset($_POST['type'])) {
    $type = $_POST['type'];
    if ($type === 'taksit') {
        taksit();
        exit;
    }
}