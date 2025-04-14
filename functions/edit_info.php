<?php
require_once 'db.php';
require_once '../mail/mail_gonder.php';
function editAriza() {
    $db = new Database();
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? controlInput($_POST['id']) : null;
    $musteri = controlInput($_POST['musteri']);
    $tel = controlInput($_POST['tel']);
    $email = controlInput($_POST['email']);
    $adres = controlInput($_POST['adres']);
    $urun_kodu_raw = controlInput($_POST['urun_kodu']);
    $seri_no_raw = controlInput($_POST['seri_no']);
    $adet_raw = controlInput($_POST['adet']);
    $aciklama = controlInput($_POST['aciklama']);
    $ad_soyad = controlInput($_POST['ad_soyad']);
    $fatura_no = controlInput($_POST['fatura_no']);
    $teslim_alan = !empty($_POST['teslim_alan']) ? controlInput($_POST['teslim_alan']) : null;
    $kargo_firmasi = controlInput($_POST['kargo_firmasi']);
    $gonderim_sekli = controlInput($_POST['gonderim_sekli']);
    $onay = controlInput($_POST['onay']);

    $durum = '1';
    $gunceltarih = date("ymd");
    $takip_kodu = 'NEB' . $gunceltarih . random_int(1000, 9999);
    $SILINDI = 0;
    $tekniker = 0;

    if (empty($urun_kodu_raw)) {
        http_response_code(400);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(500);
        exit();
    }

    $urun_kodu_array = explode(',', $urun_kodu_raw);
    $seri_no_array = explode(',', $seri_no_raw);
    $adet_array = explode(',', $adet_raw);

    if (!empty($musteri) && !empty($tel) && !empty($email) && !empty($adres) && !empty($ad_soyad) && !empty($aciklama) && ($onay == '1') && !empty($gonderim_sekli)) {
        if ($gonderim_sekli == '1' && empty($kargo_firmasi)) {
            http_response_code(400);
            return;
        }

        $params = [
            'takip_kodu' => $takip_kodu,
            'fatura_no' => $fatura_no,
            'musteri' => $musteri,
            'tel' => $tel,
            'mail' => $email,
            'adres' => $adres,
            'aciklama' => $aciklama,
            'teslim_eden' => $ad_soyad,
            'SILINDI' => $SILINDI,
            'gonderim_sekli' => $gonderim_sekli,
            'kargo_firmasi' => $kargo_firmasi,
            'tekniker' => $tekniker
        ];

        if (!is_null($id)) {
            $params[':uye_id'] = $id;

            $query = "INSERT INTO nokta_teknik_destek 
                (uye_id, takip_kodu, fatura_no, musteri, tel, mail, adres, aciklama, teslim_eden, SILINDI, gonderim_sekli, kargo_firmasi, tekniker) 
                VALUES (:uye_id, :takip_kodu, :fatura_no, :musteri, :tel, :mail, :adres, :aciklama, :teslim_eden, :SILINDI, :gonderim_sekli, :kargo_firmasi, :tekniker)";
        } else {
            $params['teslim_alan'] = $teslim_alan;
            $query = "INSERT INTO nokta_teknik_destek 
                (takip_kodu, fatura_no, musteri, tel, mail, adres, aciklama, teslim_eden, teslim_alan, SILINDI, gonderim_sekli, kargo_firmasi, tekniker) 
                VALUES (:takip_kodu, :fatura_no, :musteri, :tel, :mail, :adres, :aciklama, :teslim_eden, :teslim_alan, :SILINDI, :gonderim_sekli, :kargo_firmasi, :tekniker)";
        }

        $db->insert($query, $params);
        $lastInsertId = $db->lastInsertId();

        // Ürünleri teknik_destek_urunler tablosuna ekle
        foreach ($urun_kodu_array as $index => $urun_kodu) {
            $seri_no = isset($seri_no_array[$index]) ? $seri_no_array[$index] : '';
            $adet = isset($adet_array[$index]) ? $adet_array[$index] : '';

            $urun_params = [
                'tdp_id' => $lastInsertId,
                'urun_kodu' => $urun_kodu,
                'seri_no' => $seri_no,
                'adet' => $adet,
                'urun_durumu' => "1",
                'SILINDI' => $SILINDI
            ];

            $urun_query = "INSERT INTO teknik_destek_urunler 
                (tdp_id, urun_kodu, seri_no, adet, urun_durumu, SILINDI) 
                VALUES (:tdp_id, :urun_kodu, :seri_no, :adet, :urun_durumu, :SILINDI)";

            $db->insert($urun_query, $urun_params);
        }
        echo $takip_kodu;

        // Mail gönder
        $mail_icerik = arizaKayitMail($musteri, $takip_kodu);
        mailGonder($email, 'Arıza Kaydınız Alınmıştır!', $mail_icerik, 'Nokta Elektronik');
    } else {
        http_response_code(400);
        exit();
    }
}
if (isset($_POST['type'])) {
    $type = $_POST['type'];
    if ($type === 'ariza') {
      editAriza();
        exit;
    }
}