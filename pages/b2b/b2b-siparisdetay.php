<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'b2b-siparisdetay';
$template = new Template('Sipariş Detay - NEBSİS', $currentPage);
$template->head();
$database = new Database();

$q = "SELECT * FROM b2b_siparisler WHERE id =:id";
$params = [
    'id' => $_GET['id']
];
$siparis = $database->fetch($q, $params);

$uye_id = $siparis['uye_id'];

$q = "SELECT * FROM uyeler WHERE id =:id";
$params = [
    'id' => $uye_id
];
$uye = $database->fetch($q, $params);

$siparis_id = $_GET['id'];

$q = "SELECT * FROM b2b_adresler WHERE uye_id =:id AND adres_turu = :teslimat ";
$params = [
    'id' => $uye_id,
    'teslimat' => 'teslimat'
];
$adres = $database->fetch($q, $params);
?>
    <style>
        label{ font-weight: bold; }
    </style>
     <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
        <?php $template->header(); ?>
               <div class="container dashboard-content ">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-5">
                            <div class="nav-align-top">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#genel-bilgiler" aria-controls="genel-bilgiler" role="tab" aria-selected="true"><span class="d-sm-none">Genel Bilgiler</span><span class="d-none d-sm-block">Genel Bilgiler</span></button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#adresler" aria-controls="adresler" role="tab" aria-selected="true"><span class="d-sm-none">Adresler</span><span class="d-none d-sm-block">Adresler</span></button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#ekbilgi" aria-controls="ekbilgi" role="tab" aria-selected="true"><span class="d-sm-none">Ek Bilgiler</span><span class="d-none d-sm-block">Ek Bilgiler</span></button>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="genel-bilgiler" role="tabpanel">
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 ">
                                            <div class="card">
                                                <div class="card-header font-weight-bold">Sipariş Genel Bilgiler</div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="uye_firma_adi" >Üye</label>
                                                        <input id="uye_firma_adi" type="text" value="<?php echo $uye['firmaUnvani'] ?>" readonly class="form-control form-control-sm" >
                                                    </div>
                                                    <div class="form-group mt-3">
                                                        <label for="uye_email" >Email</label>
                                                        <input type="text" name="uye_id" id="uye_id" hidden >
                                                        <input id="uye_email" type="email" name="uye_email" readonly class="form-control form-control-sm" value="<?php echo $uye['email'] ?>">
                                                    </div>
                                                    <div class="form-group mt-3">
                                                        <label for="siparis_no" >Sipariş No</label>
                                                        <input id="siparis_no" type="text" name="siparis_no" value="<?php echo $siparis['siparis_no'] ?>" readonly class="form-control form-control-sm" >
                                                    </div>
                                                    <div class="form-group mt-3">
                                                        <label for="siparis_tarihi">Sipariş Tarihi</label>
                                                        <input id="siparis_tarihi" type="text" name="siparis_tarihi" value="<?php echo $siparis['tarih'] ?>" readonly class="form-control form-control-sm" >
                                                    </div>
                                                    <div class="form-group mt-3">
                                                        <label for="siparis_durum">Sipariş Durumu</label>
                                                        <select id="siparis_durum" name="siparis_durum" class="form-control form-control-sm" disabled>
                                                            <?php
                                                            $q = "SELECT * FROM b2b_siparis_durum";
                                                            $durumlar = $database->fetchAll($q);

                                                            // The desired id to be selected
                                                            $selectedId = $siparis['durum']; // Change this to the desired id

                                                            foreach ($durumlar as $durum) {
                                                                // Check if the current option's id matches the desired id
                                                                $selected = ($durum['id'] == $selectedId) ? 'selected' : '';
                                                                ?>
                                                                <option value='<?php echo $durum['id']; ?>' <?php echo $selected; ?>><?php echo $durum['durum']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group mt-3 ">
                                                        <label for="odeme_durum" >Ödeme Şekli</label>
                                                        <select id="odeme_durum" name="odeme_durum" class="form-control form-control-sm" disabled>
                                                            <option value="kart">Kart</option>
                                                            <option value="havale">Havale</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 ">
                                            <div class="card">
                                                <div class="card-header font-weight-bold">Kargo Bilgileri</div>
                                                <div class="card-body">
                                                    <?php
                                                    $kargo_id = $siparis['kargo_firmasi'];
                                                    switch ($kargo_id) {
                                                        case '0':
                                                            $kargo_adi = "Mağazadan Teslim Alınacak";
                                                            break;
                                                        case '1':
                                                            $kargo_adi = "Özel Kargo";
                                                            break;
                                                        case '2':
                                                            $kargo_adi = "Yurtiçi Kargo";
                                                            break;
                                                        default:
                                                            $kargo_adi = "";
                                                            break;
                                                    }
                                                    ?>
                                                    <form action="https://www.noktaelektronik.com.tr/php/edit_info.php" method="post">
                                                        <div class="form-group">
                                                            <label for="kargoFirmasi" >Kargo Firmasi</label>
                                                            <input id="kargoFirmasi" type="text" value="<?php echo $kargo_adi ?>" readonly class="form-control form-control-sm" >
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <label for="koli" >Koli Adedi</label>
                                                            <input hidden type="text" name="sip_id" value="<?php echo $siparis['id'] ?>">
                                                            <input id="koli" type="text" name="koli" value="<?php echo $siparis['koli'] ?>" class="form-control form-control-sm" >
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <label for="desi" >Desi</label>
                                                            <input id="desi" type="text" value="<?php echo $siparis['desi'] ?>" readonly class="form-control form-control-sm" >
                                                        </div>
                                                        <button class="btn btn-primary mt-3" type="submit" name="kargoKoliKaydet">Kaydet</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <div class="card">
                                                <div class="card-header font-weight-bold">Sipariş Ürünler</div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-bordered second">
                                                            <thead class="bg-light">
                                                                <th class="p-2 border-right text-center">Fotoğraf</th>
                                                                <th class="p-2 border-right text-center">Stok Kodu</th>
                                                                <th class="p-2 border-right text-center">Ürün Adı</th>
                                                                <th class="p-2 border-right text-center">Miktar</th>
                                                                <th class="p-2 border-right text-center">Birim Fiyatı TL</th>
                                                                <th class="p-2 border-right text-center">KDV Hariç Toplam Fiyatı TL</th>
                                                                <th class="p-2 border-right text-center">KDV Dahil Toplam Fiyatı TL</th>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                            $q = "SELECT 
                                                            b2b_siparis_urunler.*,
                                                            nokta_urunler.UrunKodu,
                                                            nokta_urunler.UrunAdiTR,
                                                            nokta_urunler.seo_link,
                                                            nokta_urunler.kdv,
                                                            COALESCE(MIN(nokta_urunler_resimler.KResim), 'gorsel_hazirlaniyor.jpg') AS foto
                                                        FROM b2b_siparis_urunler
                                                        LEFT JOIN nokta_urunler ON b2b_siparis_urunler.BLKODU = nokta_urunler.BLKODU
                                                        LEFT JOIN nokta_urunler_resimler ON nokta_urunler.id = nokta_urunler_resimler.UrunID
                                                        WHERE b2b_siparis_urunler.sip_id = $siparis_id
                                                        GROUP BY b2b_siparis_urunler.BLKODU";

                                                            if ( $d = $database->fetchAll($q) ){foreach( $d as $k => $row ) {
                                                                global $total_sum;
                                                                if(!empty($row["DSF4"])) {
                                                                    $total_price = $row['birim_fiyat'] * $row["adet"];
                                                                    $total_sum += $total_price;
                                                                }else{
                                                                    $total_price = $row['birim_fiyat'] * $row["dolar_satis"] * $row["adet"];
                                                                    $total_sum += $total_price;
                                                                }
                                                            ?>
                                                                <tr>
                                                                    <td class="text-center border-right"><div class="m-r-10"><img width="45" class="rounded" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/<?php echo $row['foto']; ?>"/></div></td>
                                                                    <td class="text-center border-right"><?php echo $row['UrunKodu']; ?></td>
                                                                    <td class="text-center border-right"><?php echo $row['UrunAdiTR']; ?></td>
                                                                    <td class="text-center border-right"><?php echo $row['adet']; ?></td>
                                                                    <td class="text-center border-right">₺<?php
                                                                        if(!empty($row["DSF4"])){
                                                                            echo $row['birim_fiyat'];
                                                                        }else{
                                                                            echo $row['birim_fiyat'] * $row["dolar_satis"];
                                                                        }
                                                                            ?>
                                                                    </td>
                                                                    <td class="text-center border-right">₺<?php echo $total_price; ?></td>
                                                                    <td class="text-center border-right">₺<?php echo $total_price * 1.20; ?></td>
                                                                </tr>
                                                            <?php } } ?>
                                                            <?php if($siparis["indirim"] != '0.00'){ ?>
                                                                <tr class="table-info">
                                                                    <td class="text-center border-right font-weight-bold">İndirim</td>
                                                                    <td class="text-center border-right"></td>
                                                                    <td class="text-center border-right"></td>
                                                                    <td class="text-center border-right"></td>
                                                                    <td class="text-center border-right"></td>
                                                                    <td class="text-center border-right"></td>
                                                                    <td class="text-center border-right font-weight-bold">₺<?= $siparis["indirim"] ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                            <?php if($siparis["kargo_firmasi"] != 0){ ?>
                                                                <tr class="table-info">
                                                                    <td class="text-center border-right font-weight-bold">Kargo Gönderim Ücreti</td>
                                                                    <td class="text-center border-right"></td>
                                                                    <td class="text-center border-right"></td>
                                                                    <td class="text-center border-right"></td>
                                                                    <td class="text-center border-right"></td>
                                                                    <td class="text-center border-right"></td>
                                                                    <td class="text-center border-right font-weight-bold">₺<?= $siparis["kargo_ucreti"] ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                                <tr class="table-success">
                                                                    <td class="text-center border-right font-weight-bold">Genel Toplam</td>
                                                                    <td class="text-center border-right"></td>
                                                                    <td class="text-center border-right"></td>
                                                                    <td class="text-center border-right"></td>
                                                                    <td class="text-center border-right"></td>
                                                                    <td class="text-center border-right"></td>
                                                                    <td class="text-center border-right font-weight-bold">
                                                                        <?php
                                                                        if($siparis["kargo_firmasi"] != 0 && $siparis["indirim"] != '0.00'){
                                                                            $kargo_ucreti = $siparis["kargo_ucreti"];
                                                                            $siparis_indirim = $siparis["indirim"];
                                                                            echo '₺' . ($total_sum * 1.20 + $kargo_ucreti) - $siparis_indirim;
                                                                        }elseif($siparis["kargo_firmasi"] != 0){
                                                                            $kargo_ucreti = $siparis["kargo_ucreti"];
                                                                            echo '₺' . $total_sum * 1.20 + $kargo_ucreti;
                                                                        }elseif ($siparis["indirim"] != '0.00'){
                                                                            $siparis_indirim = $siparis["indirim"];
                                                                            echo '₺' . ($total_sum * 1.20) - $siparis_indirim;
                                                                        }else{
                                                                            echo '₺' . $total_sum * 1.20;
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="adresler" role="tabpanel">
                                    <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 ">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="fatura_adresi" >Fatura Adresi</label>
                                                    <input id="fatura_adresi" type="text" name="fatura_adresi" readonly required class="form-control form-control-sm" >
                                                </div>
                                                <div class="form-group">
                                                    <label for="ad_soyad2" >Adı Soyadı</label>
                                                    <input type="text" name="uye_id" id="uye_id" hidden >
                                                    <input id="ad_soyad2" type="text" name="ad_soyad2" readonly value="<?php echo $uye['ad'] . ' ' . $uye['soyad'] ?>" class="form-control form-control-sm-sm">
                                                </div>
                                                <div class="form-group">
                                                    <label for="kimlik_no2" >Kimlik No</label>
                                                    <input id="kimlik_no2" type="text" name="kimlik_no2" value="<?php echo $uye['tc_no'] ?>" readonly class="form-control form-control-sm" >
                                                </div>
                                                <div class="form-group">
                                                    <label for="ticari_unvan2">Ticari Ünvan</label>
                                                    <input id="ticari_unvan2" type="text" name="ticari_unvan2" value="<?php echo $uye['firmaUnvani'] ?>" readonly class="form-control form-control-sm" >
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="vergi_dairesi">Vergi Dairesi</label>
                                                            <input id="vergi_dairesi" type="text" name="vergi_dairesi" value="<?php echo $uye['vergi_dairesi'] ?>" readonly class="form-control form-control-sm" >
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="vergi_no2" >Vergi No</label>
                                                            <input id="vergi_no2" type="text" name="vergi_no2" value="<?php echo $uye['vergi_no'] ?>" readonly class="form-control form-control-sm" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="teslimat_adresi" >Adres</label>
                                                    <textarea name="teslimat_adresi" id="teslimat_adresi" readonly class="form-control form-control-sm"><?= $siparis['uye_adres']?></textarea>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="ulke2">Ülke</label>
                                                            <input id="ulke2" type="text" name="ulke2" value="<?php echo $uye['ulke'] ?>" readonly required class="form-control form-control-sm" >
                                                        </div>
                                                    </div>
                                                    <?php
                                                    $q = "SELECT * FROM iller WHERE il_id =:id";
                                                    $params = [
                                                        'id' => $uye["il"]
                                                    ];
                                                    $uyeil = $database->fetch($q, $params);

                                                    $q = "SELECT * FROM ilceler WHERE ilce_id =:id";
                                                    $params = [
                                                        'id' => $uye["ilce"]
                                                    ];
                                                    $uyeilce = $database->fetch($q, $params);
                                                    ?>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="sehir2" >Şehir</label>
                                                            <input id="sehir2" type="text" name="sehir2" value="<?php echo $uyeil['il_adi'] ?>" readonly class="form-control form-control-sm" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="ilce2">İlçe</label>
                                                            <input id="ilce2" type="text" name="ilce2" value="<?php echo $uyeilce['ilce_adi'] ?>" readonly class="form-control form-control-sm" >
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="semt2" >Semt</label>
                                                            <input id="semt2" type="text" name="semt2" readonly class="form-control form-control-sm" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="posta_kodu2">Posta Kodu</label>
                                                            <input id="posta_kodu2" type="text" name="posta_kodu2" value="<?php echo $uye['posta_kodu'] ?>" readonly class="form-control form-control-sm" >
                                                        </div>
                                                    </div>
                                                    <div class="col"></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="telefon2">Telefon</label>
                                                            <input id="telefon2" type="text" name="telefon2" value="<?php echo $uye['sabit_tel'] ?>" readonly class="form-control form-control-sm" >
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="mobil_tel2" >Mobil Telefon</label>
                                                            <input id="mobil_tel2" type="text" name="mobil_tel2" value="<?php echo $uye['tel'] ?>" readonly class="form-control form-control-sm" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 ">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="teslimat_adresi" >Teslimat Adresi</label>
                                                    <input id="teslimat_adresi" type="text" name="teslimat_adresi" readonly required class="form-control form-control-sm" >
                                                </div>
                                                <div class="form-group">
                                                    <label for="ad_soyad2" >Adı Soyadı</label>
                                                    <input type="text" name="uye_id" id="uye_id" hidden >
                                                    <input id="ad_soyad2" type="text" name="ad_soyad2" readonly value="<?php echo $siparis['teslimat_ad'] . ' ' . $siparis['teslimat_soyad'] ?>" class="form-control form-control-sm-sm">
                                                </div>
                                                <div class="form-group">
                                                    <label for="kimlik_no2" >Kimlik No</label>
                                                    <input id="kimlik_no2" type="text" name="kimlik_no2" value="<?php echo $siparis['teslimat_tcno'] ?>" readonly class="form-control form-control-sm" >
                                                </div>
                                                <div class="form-group">
                                                    <label for="ticari_unvan2">Ticari Ünvan</label>
                                                    <input id="ticari_unvan2" type="text" name="ticari_unvan2" value="<?php echo $siparis['teslimat_firmaadi'] ?>" readonly class="form-control form-control-sm" >
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                            <div class="form-group">
                                                                <label for="vergi_dairesi">Vergi Dairesi</label>
                                                                <input id="vergi_dairesi" type="text" name="vergi_dairesi" value="<?php echo $siparis['teslimat_vergidairesi'] ?>" readonly class="form-control form-control-sm" >
                                                            </div>
                                                    </div>
                                                    <div class="col">
                                                            <div class="form-group">
                                                                <label for="vergi_no2" >Vergi No</label>
                                                                <input id="vergi_no2" type="text" name="vergi_no2" value="<?php echo $siparis['teslimat_vergino'] ?>" readonly class="form-control form-control-sm" >
                                                            </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="teslimat_adresi" >Adres</label>
                                                    <textarea name="teslimat_adresi" id="teslimat_adresi" readonly class="form-control form-control-sm"><?= $siparis['teslimat_adres']?></textarea>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="ulke2">Ülke</label>
                                                            <input id="ulke2" type="text" name="ulke2" value="<?php echo $siparis['teslimat_ulke'] ?>" readonly required class="form-control form-control-sm" >
                                                        </div>
                                                    </div>
                                                    <?php
                                                    $q = "SELECT * FROM iller WHERE il_id =:id";
                                                    $params = [
                                                        'id' => $siparis["teslimat_il"]
                                                    ];
                                                    $uyeil = $database->fetch($q, $params);

                                                    $q = "SELECT * FROM ilceler WHERE ilce_id =:id";
                                                    $params = [
                                                        'id' => $siparis["teslimat_ilce"]
                                                    ];
                                                    $uyeilce = $database->fetch($q, $params);
                                                    ?>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="sehir2" >Şehir</label>
                                                            <input id="sehir2" type="text" name="sehir2" value="<?php echo $uyeil['il_adi'] ?>" readonly class="form-control form-control-sm" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="ilce2">İlçe</label>
                                                            <input id="ilce2" type="text" name="ilce2" value="<?php echo $uyeilce['ilce_adi'] ?>" readonly class="form-control form-control-sm" >
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="semt2" >Semt</label>
                                                            <input id="semt2" type="text" name="semt2" readonly class="form-control form-control-sm" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="posta_kodu2">Posta Kodu</label>
                                                            <input id="posta_kodu2" type="text" name="posta_kodu2" value="<?php echo $siparis['teslimat_postakodu'] ?>" readonly class="form-control form-control-sm" >
                                                        </div>
                                                    </div>
                                                    <div class="col"></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="mobil_tel2" >Mobil Telefon</label>
                                                            <input id="mobil_tel2" type="text" name="mobil_tel2" value="<?php echo $siparis['teslimat_telefon'] ?>" readonly class="form-control form-control-sm" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="ekbilgi" role="tabpanel">
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 ">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="ip" >IP</label>
                                                        <input id="ip" type="text" name="ip" required class="form-control form-control-sm" >
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label for="ek_bilgi" >Ek Bilgi</label>
                                                        <input id="ek_bilgi" type="text" name="ek_bilgi" required class="form-control form-control-sm" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="ek_bilgi1" >Ek Bilgi 1</label>
                                                        <input id="ek_bilgi1" type="text" name="ek_bilgi1" required class="form-control form-control-sm" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="ek_bilgi2" >Ek Bilgi 2</label>
                                                        <input id="ek_bilgi2" type="text" name="ek_bilgi2" required class="form-control form-control-sm" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="ek_bilgi3" >Ek Bilgi 3</label>
                                                        <input id="ek_bilgi3" type="text" name="ek_bilgi3" required class="form-control form-control-sm" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="ek_bilgi4" >Ek Bilgi 4</label>
                                                        <input id="ek_bilgi4" type="text" name="ek_bilgi4" required class="form-control form-control-sm" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="ek_bilgi5" >Ek Bilgi 5</label>
                                                        <input id="ek_bilgi5" type="text" name="ek_bilgi5" required class="form-control form-control-sm" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="promosyon_kodu" >Promosyon Kodu</label>
                                                        <input id="promosyon_kodu" type="text" name="promosyon_kodu" required class="form-control form-control-sm" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="kargo_firmasi" >Kargo Firması</label>
                                                        <input id="kargo_firmasi" type="text" name="kargo_firmasi" required class="form-control form-control-sm" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="kargo_barkodu" >Kargo Barkodu</label>
                                                        <input id="kargo_barkodu" type="text" name="kargo_barkodu" required class="form-control form-control-sm" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="kargo_no" >Kargo No</label>
                                                        <input id="kargo_no" type="text" name="kargo_no" required class="form-control form-control-sm" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="kargo_tarihi" >Kargo Tarihi</label>
                                                        <input id="kargo_tarihi" type="text" name="kargo_tarihi" required class="form-control form-control-sm" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="koli_adedi" >Koli Adedi</label>
                                                        <input id="koli_adedi" type="text" name="koli_adedi" required class="form-control form-control-sm" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="teslim_tarihi" >Teslim Tarihi</label>
                                                        <input id="teslim_tarihi" type="text" name="teslim_tarihi" required class="form-control form-control-sm" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
               </div>
          </div>
     </div>
</div>
</body>
</html>
<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/libs/i18n/i18n.js"></script>
<script src="assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/form-layouts.js"></script>
<script>
$(document).ready(function() {
         // Kullanıcı yetkilerini seçme işlevselliği
     $(document).on('click', '.custom-control-input', function() {
          var selectedYetkiler = [];
          // Tüm seçili yetkileri bul
          $('.custom-control-input:checked').each(function() {
               selectedYetkiler.push($(this).val());
          });
          // Seçili yetkileri virgülle birleştirerek gizli alana ekleyin
          $('#selectedYetkiler').val(selectedYetkiler.join(','));
     });
});
</script>

<script>
$(document).ready(function() {
    // Initially hide the fields
    $("#vergi").show();
    $("#kimlik").hide();

    // Add change event listener to the checkbox
    $("#vrgtc").change(function() {
        // If the checkbox is checked, hide kimlik and show vergi
        if ($(this).is(":checked")) {
            $("#vergi").hide();
            $("#kimlik").show();
        } else {
            // If the checkbox is not checked, show kimlik and hide vergi
            $("#vergi").show();
            $("#kimlik").hide();
        }
    });
});
</script>