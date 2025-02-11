<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'b2b-uyeler';
$template = new Template('Üyeler - NEBSİS',  $currentPage);
// head'i çağırıyoruz
$template->head();
$database = new Database();

$id = $_GET['id'] ?? null; // ID kontrolü yap
if (!$id || !is_numeric($id)) {
    die("Geçersiz ID"); // Güvenlik önlemi
}
$uye_id = $id;

$kullan = $database->fetch("SELECT * FROM uyeler WHERE id = :id", ['id' => $id]);
if (!$kullan) {
    die("Kullanıcı bulunamadı.");
}
$satis_temsilcisi_id = $kullan["satis_temsilcisi"];
$il = $kullan["il"];
$ilce = $kullan["ilce"];
$uye_tipi = $kullan["uye_tipi"];
$fiyat = $kullan['fiyat'];

$satis_temsilcileri = $database->fetchAll("SELECT * FROM users WHERE roles LIKE '%5%' ");

$zorunlu = "<span style='color:red;'>*</span>"
?>
<body>
    <style>
        .form-group{margin-top: 10px;}
    </style>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php $template->header(); ?>
        <!-- Content wrapper -->
        <div class="content-wrapper">
            <!-- Content -->
            <div class=" flex-grow-1 container-p-y container-xxl">
                <form action="functions/uyeler/uye_guncelle.php" method="post">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Genel Bilgiler</h5>
                                <div class="card-body">
                                    <div class="">
                                        <label for="uyeKayitTipi" >Kayıt Tipi</label>
                                        <select id="uyeKayitTipi" name="uyeKayitTipi" class="form-control form-control-sm">
                                            <option value='Üye' <?php if ($uye_tipi == 'Üye') echo 'selected'; ?>>Üye</option>
                                            <option value='Bayi' <?php if ($uye_tipi == 'Bayi') echo 'selected'; ?>>Bayi</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="uye_email" >Email <?= $zorunlu ?></label>
                                        <input type="text" name="uye_id" id="uye_id" hidden value="<?= $kullan["id"] ?>">
                                        <input id="uye_email" type="email" name="uye_email" required placeholder="" autocomplete="off" class="form-control form-control-sm" value="<?php echo $kullan['email'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="muhasebe_kodu">Muhasebe Kodu</label>
                                        <input id="muhasebe_kodu" name="muhasebe_kodu" type="text" required class="form-control form-control-sm" value="<?= $kullan['muhasebe_kodu'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="uye_parola" >Şifre</label>
                                        <input id="uye_parola" type="password" name="uye_parola" placeholder="Değiştirmek için buraya yeni şifre yazınız." class="form-control form-control-sm">
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="uye_adi" >Adı <?= $zorunlu ?></label>
                                                <input id="uye_adi" type="text" name="uye_adi" class="form-control form-control-sm" value="<?= $kullan['ad'] ?>">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="uye_soyadi" >Soyadı <?= $zorunlu ?></label>
                                                <input id="uye_soyadi" type="text" name="uye_soyadi" class="form-control form-control-sm" value="<?= $kullan['soyad'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="uye_firma_adi" >Firma Adı <?= $zorunlu ?></label>
                                        <input id="uye_firma_adi" type="text" name="uye_firma_adi" required class="form-control form-control-sm" value="<?= $kullan['firmaUnvani'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <div class="checkbox ml-4">
                                            <input type="checkbox" id="vrgtc" name="vrgtc" class=""/><label for="vrgtc"> Vergi Numarası Yerine TC Kimlik Numarası</label>
                                        </div>
                                    </div>
                                    <div class="form-group" id="kimlik">
                                        <label for="uye_kimlik_no" >Kimlik No <?= $zorunlu ?></label>
                                        <input id="uye_kimlik_no" type="text" name="uye_kimlik_no" class="form-control form-control-sm" value="<?= $kullan['tc_no'] ?>">
                                    </div>
                                    <div class="form-group" id="vergi">
                                        <label for="uye_vergi_no" >Vergi No <?= $zorunlu ?></label>
                                        <input id="uye_vergi_no" type="text" name="uye_vergi_no" class="form-control form-control-sm" value="<?= $kullan['vergi_no'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="uye_vergi_dairesi" >Vergi Dairesi <?= $zorunlu ?></label>
                                        <input id="uye_vergi_dairesi" type="text" name="uye_vergi_dairesi" required class="form-control form-control-sm" value="<?= $kullan['vergi_dairesi'] ?>">
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="uye_fiyat_no" >Kullanılacak Fiyat No <?= $zorunlu ?></label>
                                                <select id="uye_fiyat_no" name="uye_fiyat_no" class="form-control form-control-sm">
                                                    <option value='4' <?php if ($fiyat == '4') echo 'selected'; ?>>Fiyat 4</option>
                                                    <option value='3' <?php if ($fiyat == '3') echo 'selected'; ?>>Fiyat 3</option>
                                                    <option value='2' <?php if ($fiyat == '2') echo 'selected'; ?>>Fiyat 2</option>
                                                    <option value='1' <?php if ($fiyat == '1') echo 'selected'; ?>>Fiyat 1</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="uye_durum">Durum</label>
                                                <select id="uye_durum" name="uye_durum" class="form-control form-control-sm">
                                                    <option value="1" <?php echo ($kullan['aktif'] == 1) ? 'selected' : ''; ?>>Aktif</option>
                                                    <option value="0" <?php echo ($kullan['aktif'] == 0) ? 'selected' : ''; ?>>Pasif</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="uye_aktivasyon">Aktivasyon</label>
                                                <select id="uye_aktivasyon" name="uye_aktivasyon" class="form-control form-control-sm">
                                                    <option value="1" <?php echo ($kullan['aktivasyon'] == 1) ? 'selected' : ''; ?>>Yapıldı</option>
                                                    <option value="0" <?php echo ($kullan['aktivasyon'] == 0) ? 'selected' : ''; ?>>Yapılmadı</option>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                    <label for="uye_satis_temsilcisi" >Satış Temsilcisi <?= $zorunlu ?></label>
                                                    <select id="uye_satis_temsilcisi" name="uye_satis_temsilcisi" class="form-control form-control-sm">
                                                        <option value="">Seçiniz...</option>
                                                        <?php
                                                        foreach($satis_temsilcileri as $row) {
                                                            $selected = ($row["id"] == $satis_temsilcisi_id) ? 'selected' : '';
                                                        ?>
                                                            <option value='<?php echo $row['id']; ?>' <?php echo $selected; ?>><?= $row["full_name"]; ?></option>
                                                        <?php } ?>
                                                    </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="vergi_levhasi">Vergi Levhasi</label>
                                                <a class="form-control form-control-sm" id="vergi_levhasi" name="vergi_levhasi" target="_blank" href="https://www.noktaelektronik.com.tr/assets/uploads/vergi_levhalari/<?= $kullan["vergi_levhasi"] ?>"><i class="fa-solid fa-file-arrow-down fa-xl"></i> İndirmek için tıkla!</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="card">
                                        <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">İletişim Bilgileri</h5>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="location" >Ülke/Şehir/İlçe/Semt <?= $zorunlu ?></label>
                                                <div class="row">
                                                    <div class="col">
                                                        <select class="form-control form-control-sm">
                                                            <option>Türkiye</option>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <select id="il" name="il" class="form-control form-control-sm">
                                                            <?php if($il == ''){ ?>
                                                                <option>İl <?= $zorunlu ?></option>
                                                            <?php }else {
                                                                $ils = $database->fetch("SELECT il_adi FROM iller WHERE il_id = $il "); 
                                                                $uye_il_adi = $ils['il_adi']; ?>
                                                                <option value="<?= $il ?>"><?= $uye_il_adi ?></option>
                                                            <?php } ?>
                                                            <?php $iller = $database->fetchAll("SELECT * FROM iller "); 
                                                            foreach($iller as $row) { ?>
                                                            <option id="ilce_id" value="<?= $kullan["ilce"]; ?>" hidden></option>
                                                            <option id="mah_id" value="<?= $kullan["mahalle"]; ?>" hidden></option>
                                                            <option value="<?= $row['il_id'] ?>"><?= $row["il_adi"] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <select class="form-control form-control-sm" id="ilce" name="ilce"></select>
                                                    </div>
                                                    <div class="col">
                                                        <select class="form-control form-control-sm" id="mahalle" name="mahalle"></select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="uye_adres" >Adres <?= $zorunlu ?></label>
                                                <textarea class="form-control" name="uye_adres" id="uye_adres" cols="10" rows="5" required><?= $kullan['adres'] ?></textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="form-group">
                                                        <label for="uye_posta_kodu" >Posta Kodu <?= $zorunlu ?></label>
                                                        <input id="uye_posta_kodu" type="text" name="uye_posta_kodu" class="form-control form-control-sm" value="<?= $kullan['posta_kodu'] ?>">
                                                    </div>
                                                </div>

                                                <div class="col-5">
                                                    <div class="form-group">
                                                        <label for="uye_cep_tel" >Cep Telefonu <?= $zorunlu ?></label>
                                                        <input id="uye_cep_tel" type="text" name="uye_cep_tel" required class="form-control form-control-sm" value="<?= $kullan['tel'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label for="uye_sabit_tel" >Sabit Telefon</label>
                                                        <input id="uye_sabit_tel" type="text" name="uye_sabit_tel" class="form-control form-control-sm" value="<?= $kullan['sabit_tel'] ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 pl-0 mt-5">
                                <p class="text-center">
                                    <button type="submit" name="uye_guncelle" class="btn btn-space btn-primary font-weight-bold">Kaydet</button>
                                </p>
                            </div>
                        </div>
                    </form>
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 ">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 style="color:#0a78f1">Toplam Cari Ödeme (Aylık)</h5>
                                        <div class="metric-value d-inline-block">
                                            <?php
                                            // İçinde bulunduğumuz ay ve yılı al
                                            $currentMonthYear = date('Y-m');

                                            // Mevcut ayın toplam tutarını al
                                            $currentMonthData = $database->fetch(
                                                "SELECT SUM(tutar) AS toplam_tutar FROM b2b_sanal_pos_odemeler WHERE basarili = '1' AND uye_id = :uye_id AND islem_turu = 'cari' 
                                                AND DATE_FORMAT(tarih, '%Y-%m') = :current_month_year", ['uye_id' => $uye_id, 'current_month_year' => $currentMonthYear]
                                            );
                                            $currentMonthTotal = $currentMonthData['toplam_tutar'] ?? 0;

                                            // Bir önceki ayı hesapla
                                            $previousMonthYear = date('Y-m', strtotime('-1 month'));

                                            // Önceki ayın toplam tutarını al
                                            $previousMonthData = $database->fetch(
                                                "SELECT SUM(tutar) AS toplam_tutar FROM b2b_sanal_pos_odemeler WHERE basarili = '1' AND uye_id = :uye_id AND islem_turu = 'cari' 
                                                AND DATE_FORMAT(tarih, '%Y-%m') = :previous_month_year", ['uye_id' => $uye_id, 'previous_month_year' => $previousMonthYear]
                                            );
                                            $previousMonthTotal = $previousMonthData['toplam_tutar'] ?? 0;

                                            // Formatlı sayılar
                                            $formattedCurrentMonthTotal = number_format($currentMonthTotal, 2, ',', '.');
                                            $formattedLastMonthTotal = number_format($previousMonthTotal, 2, ',', '.');

                                            // Yüzdelik fark hesaplama (bölme hatasını önlemek için kontrol)
                                            $percentageDifference = ($previousMonthTotal != 0)
                                                ? intval((($currentMonthTotal - $previousMonthTotal) / $previousMonthTotal) * 100)
                                                : 0;

                                            // Çıktıyı yazdır
                                            echo "<h1 class='mb-1'>₺$formattedCurrentMonthTotal</h1>";
                                            echo "<h5 class='mb-1'><span>Geçen ay : </span>₺$formattedLastMonthTotal</h5>";
                                            ?>
                                        </div>
                                        <div class="metric-label d-inline-block float-right text-success font-weight-bold">
                                            <span><i class="fa fa-fw fa-arrow-<?php echo ($percentageDifference >= 0) ? 'up' : 'down'; ?>"></i></span>
                                            <span><?php echo $percentageDifference; ?>%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 style="color:#0a78f1">Toplam Sipariş Ciro (Aylık)</h5>
                                        <div class="metric-value d-inline-block">
                                            <?php
                                            $currentMonthYear = date('Y-m');
                                            $previousMonthYear = date('Y-m', strtotime($currentMonthYear . ' -1 month'));

                                            // Mevcut ayın toplam sipariş tutarı
                                            $currentMonthTotal = $database->fetchColumn(
                                                "SELECT SUM(toplam) FROM b2b_siparisler WHERE DATE_FORMAT(tarih, '%Y-%m') = :current_month_year AND uye_id = :uye_id",
                                                ['current_month_year' => $currentMonthYear, 'uye_id' => $uye_id]
                                            ) ?? 0;

                                            // Geçen ayın toplam sipariş tutarı
                                            $lastMonthTotal = $database->fetchColumn(
                                                "SELECT SUM(toplam) FROM b2b_siparisler WHERE DATE_FORMAT(tarih, '%Y-%m') = :current_month_year AND uye_id = :uye_id",
                                                ['current_month_year' => $previousMonthYear, 'uye_id' => $uye_id]
                                            ) ?? 0;

                                            // Mevcut ayın sipariş sayısı
                                            $totalRows = $database->fetchColumn(
                                                "SELECT COUNT(*) FROM b2b_siparisler WHERE DATE_FORMAT(tarih, '%Y-%m') = :current_month_year AND uye_id = :uye_id",
                                                ['current_month_year' => $currentMonthYear, 'uye_id' => $uye_id]
                                            ) ?? 0;

                                            // Sepet kullanıcı sayısı
                                            $totalRowsUyeSepet = $database->fetchColumn(
                                                "SELECT COUNT(DISTINCT uye_id) FROM b2b_uye_sepet WHERE uye_id = :uye_id",
                                                ['uye_id' => $uye_id]
                                            ) ?? 0;

                                            // Tutarları formatla
                                            $formattedCurrentMonthTotal = number_format($currentMonthTotal, 2, ',', '.');
                                            $formattedLastMonthTotal = number_format($lastMonthTotal, 2, ',', '.');

                                            // Yüzdelik değişimi hesapla
                                            $percentageDifference = ($lastMonthTotal != 0) ? intval((($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100) : 0;
                                            ?>

                                            <h1 class="mb-1">₺<?= $formattedCurrentMonthTotal ?></h1>
                                            <h5 class="mb-1"><span>Geçen ay : </span>₺<?= $formattedLastMonthTotal ?></h5>
                                        </div>
                                        <div class="metric-label d-inline-block float-right text-success font-weight-bold">
                                            <span><i class="fa fa-fw fa-arrow-<?= ($percentageDifference >= 0) ? 'up' : 'down'; ?>"></i></span>
                                            <span><?= $percentageDifference; ?>%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-5">
                                <div class="card">
                                    <div class="card-header p-2" style="background-color: #0a78f1; color:white;">Son Siparişler</div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered second" style="width:100%;">
                                            <thead class="bg-light">
                                            <tr class="border-0">
                                                <th class="border-0 font-weight-bold">Firma Adı</th>
                                                <th class="border-0 font-weight-bold">Tarih</th>
                                                <th class="border-0 font-weight-bold">Durum</th>
                                                <th class="border-0 font-weight-bold">Ödeme</th>
                                                <th class="border-0 font-weight-bold">Toplam</th>
                                                <th class="border-0 font-weight-bold">Detay</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $siparisler = $database->fetchAll(" SELECT s.*, sd.durum FROM b2b_siparisler s
                                                        JOIN b2b_siparis_durum sd ON s.durum = sd.id WHERE s.uye_id = $uye_id
                                                        ORDER BY s.tarih DESC LIMIT 5 ");
                                                    if ($siparisler) {
                                                        foreach ($siparisler as $siparis) {
                                                ?>
                                                        <tr>
                                                            <td style="font-size:12px"><?= htmlspecialchars($siparis["uye_firmaadi"]) ?></td>
                                                            <td style="font-size:12px"><?= htmlspecialchars($siparis["tarih"]) ?></td>
                                                            <td style="font-size:12px">
                                                                <span class="btn btn-sm btn-success p-1" style="font-size: 11.5px"><?= htmlspecialchars($siparis["durum"]) ?></span>
                                                            </td>
                                                            <td style="font-size:12px">
                                                                <span class="btn btn-sm btn-warning p-1" style="font-size: 11.5px"><?= htmlspecialchars($siparis["odeme_sekli"]) ?></span>
                                                            </td>
                                                            <?php
                                                                $toplam = $siparis["toplam"];
                                                                $toplam = str_replace(',', '.', $toplam);
                                                                $toplam = (float)$toplam;
                                                                $formattedToplam = number_format($toplam, 2, ',', '.');
                                                            ?>
                                                            <td style="font-size:12px"><?= htmlspecialchars($formattedToplam, ENT_QUOTES, 'UTF-8'); ?></td>
                                                            <td>
                                                                <a style="border-color: #B197FC;" href="https://www.noktaelektronik.com.tr/admin/siparisler/adminSiparisDetay.php?id=<?= htmlspecialchars($siparis["id"]) ?>" class="btn btn-sm btn-outline-light rounded">
                                                                    <i class="fa-solid fa-pen-to-square" style="color: #B197FC;"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                <?php }} ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <?php
                                $currentYear = date('Y');
                                $revenue_data = [];

                                for ($i = 11; $i >= 0; $i--) {
                                    $month_year = date('M Y', strtotime("-$i month"));
                                    $year_month = date('Y-m', strtotime("-$i month"));

                                    // Veritabanı sorgusu Database sınıfı ile yapıldı
                                    $total_revenue = $database->fetchColumn(
                                        "SELECT SUM(toplam) FROM b2b_siparisler WHERE DATE_FORMAT(tarih, '%Y-%m') = :year_month AND uye_id = :uye_id",
                                        ['year_month' => $year_month, 'uye_id' => $uye_id]
                                    );

                                    $revenue_data[$month_year] = $total_revenue ?? 0; // Eğer veri yoksa 0 olarak ayarla
                                }

                                // Convert PHP array to JavaScript object
                                $revenue_data_json = json_encode(array_values($revenue_data));
                            ?>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-5">
                                <div class="card">
                                    <div class="card-header p-2" style="background-color: #0a78f1; color:white;">
                                        Cari Ödeme ve Sipariş Gelir (Aylık)
                                    </div>
                                    <div class="card-body">
                                        <?php
                                        $currentYear = date('Y');
                                        $revenue_data = [];

                                        for ($i = 11; $i >= 0; $i--) {
                                            $month_year = date('M Y', strtotime("-$i month"));
                                            $firstDayOfMonth = date('Y-m-01', strtotime("-$i month"));

                                            $total_revenue = $database->fetchColumn(
                                                "SELECT SUM(tutar) FROM b2b_sanal_pos_odemeler WHERE basarili = '1' AND uye_id = :uye_id 
                                                AND islem_turu = 'cari' AND YEAR(tarih) = YEAR(:first_day) AND MONTH(tarih) = MONTH(:first_day)",
                                                ['uye_id' => $uye_id, 'first_day' => $firstDayOfMonth]
                                            );

                                            $revenue_data[$month_year] = $total_revenue ?? 0; // Eğer veri yoksa 0 olarak ayarla
                                        }

                                        $revenue_data_json1 = json_encode(array_values($revenue_data));
                                        ?>
                                        <canvas id="revenueChart1"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
            <!-- Content backdrop -->
            <div class="content-backdrop fade"></div>
        </div>
        <!-- Content wrapper -->
        <?php $template->footer(); ?>
    </div>
    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
</div>
<!-- / Layout wrapper -->
<!-- Core JS -->
<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Main JS -->
<script src="assets/js/main.js"></script>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        $("#vergi").show();
        $("#kimlik").hide();

        $("#vrgtc").change(function() {
            if ($(this).is(":checked")) {
                $("#vergi").hide();
                $("#kimlik").show();
            } else {
                $("#vergi").show();
                $("#kimlik").hide();
            }
        });
    });
</script>
<script>
    var revenueData = <?php echo $revenue_data_json; ?>;
    var ctx = document.getElementById('revenueChart').getContext('2d');
    var revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [ <?php echo implode(', ', array_map(function($key) { return "'$key'"; }, array_keys($revenue_data))); ?> ],
            datasets: [{
                label: 'Sipariş Gelir (Aylık)',
                data: revenueData,
                fill: false,
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            plugins: {
                locale: {
                    locale: 'tr-TR'
                },
                tooltip: {
                    callbacks: {
                        // Format the tooltip label
                        label: function(context) {
                            var label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('tr-TR').format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>
<script>
    var revenueData1 = <?php echo $revenue_data_json1; ?>;
    var ctx1 = document.getElementById('revenueChart1').getContext('2d');
    var revenueChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: [
                <?php
                // Generate JavaScript array of labels (month-year)
                echo implode(', ', array_map(function($key) { return "'$key'"; }, array_keys($revenue_data)));
                ?>
            ],
            datasets: [{
                label: 'Cari Gelir (Aylık)',
                data: revenueData1,
                fill: true,
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)', // Veri noktası arka plan rengi
                pointBorderColor: 'rgba(54, 162, 235, 1)' // Veri noktası sınır rengi
            },{
                label: 'Sipariş Gelir (Aylık)',
                data: revenueData,
                fill: true,
                borderColor: 'rgba(32, 154, 100, 1)',
                borderWidth: 2
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            plugins: {
                locale: {locale: 'tr-TR'},
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('tr-TR').format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>
<script>
    $(document).ready(function() {
        function loadIlceler() {
            var il_id = $('#il').val();
            var ilce = $('#ilce_id').val();
            $.ajax({
                url: "functions/uyeler/ile_gore_ilce.php",
                type: "POST",
                data: {
                        il_id: il_id,
                        ilce: ilce
                },
                cache: false,
                success: function(result) {
                        $("#ilce").html(result);
                }
            });
        }
        function loadMahalle() {
            var ilce_id = $('#ilce_id').val();
            var mahalle = $('#mah_id').val();
            $.ajax({
                url: "functions/uyeler/ilceye_gore_mahalle.php",
                type: "POST",
                data: {
                        ilce_id: ilce_id,
                        mahalle : mahalle
                },
                cache: false,
                success: function(result) {
                        $("#mahalle").html(result);
                }
            });
        }
        $(document).ready(function() {
            if ($('#il').val() !== '') {
                loadIlceler();
            }
            $('#il').on('change', loadIlceler);
            if ($('#ilce').val() !== '') {
                loadMahalle();
            }
            $('#ilce').on('change', loadMahalle);
        });
    });
</script>
<script>
        const urlParams = new URLSearchParams(window.location.search);
        const sParam = urlParams.get('s');

        if (sParam === '1') {
            Swal.fire({
                icon: 'success',
                title: 'Güncelleme Kaydedilmiştir!',
                toast: true,
                position: 'bottom-end',
                timer: 2000,
                showConfirmButton: false
            });
        }
</script>
</body>
</html>
