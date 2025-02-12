<?php
include_once '../../db.php';
if (isset($_POST['kargo_guncelle'])) {
    $database = new Database();
    $kargo_id = $_POST["kargo_id"];
    $kargo_adi = $_POST['kargo_adi'];
    $sorgu_linki = $_POST['sorgu_linki'];
    $entegrasyon_firma = $_POST['entegrasyon_firma'];
    $k_adi_go = $_POST['k_adi_go'];
    $parola_go = $_POST['parola_go'];
    $k_adi_ao = $_POST['k_adi_ao'];
    $parola_ao = $_POST['parola_ao'];
    $sabit_kargo_ucreti = $_POST['sabit_kargo_ucreti'];
    $ucretsiz_kargo_devre_disi = isset($_POST['ucretsiz_kargo_devre_disi']) ? 1 : 0;
    $kargo_vergi_no = $_POST['kargo_vergi_no'];
    $aktif = $_POST['aktif'];
    $gosterim = $_POST['gosterim'];
    $minimum_gosterim_tutari = $_POST['minimum_gosterim_tutari'];
    $maksimum_gosterim_tutari = $_POST['maksimum_gosterim_tutari'];
    $maksimum_desi_miktari = $_POST['maksimum_desi_miktari'];

    $query = "UPDATE b2b_kargo SET kargo_adi = '$kargo_adi', sorgu_link = '$sorgu_linki', entegrasyon_firma = '$entegrasyon_firma', kullanici_adi_go = '$k_adi_go', 
            parola_go = '$parola_go', kullanici_adi_ao = '$k_adi_ao', parola_ao = '$parola_ao', sabit_kargo_ucreti = '$sabit_kargo_ucreti', ucretsiz_kargo_devre_disi = '$ucretsiz_kargo_devre_disi', 
            kargo_firma_vergi_no = '$kargo_vergi_no', yayin_durumu = '$aktif', gosterim = '$gosterim', minimum_gosterim_tutari = '$minimum_gosterim_tutari',
            maksimum_gosterim_tutari = '$maksimum_gosterim_tutari', maksimum_desi_miktari = '$maksimum_desi_miktari' WHERE id = '$kargo_id'";
    $database ->update($query);

    header("Location:../../../pages/b2b/b2b-kargoDuzenle?w=noktab2b&id=" . $kargo_id . "&s=1");
}