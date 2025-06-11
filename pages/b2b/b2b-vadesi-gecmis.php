<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';
use PhpOffice\PhpSpreadsheet\IOFactory;



$currentPage = 'b2b-vadesi-gecmis';
$template = new Template('Vadesi Geçmiş Borçlar - Nokta Admin',  $currentPage);
// head'i çağırıyoruz
$template->head();
$database = new Database();

if (isset($_POST['temizle'])) {
    $database->delete("DELETE FROM vadesi_gecmis_borclar");
    echo "<div class='alert alert-success'>Tablo başarıyla temizlendi.</div>";
}

if (isset($_POST['yukle'])) {
    $dosya = $_FILES['excel']['tmp_name'];
    if ($dosya) {
        $spreadsheet = IOFactory::load($dosya);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $sayac = 0;
        foreach ($rows as $index => $row) {
            if ($index === 0) continue;

            $cari_kodu = trim($row[0]);
            $plasiyer = trim($row[1]);
            $ticari_unvani = trim($row[2]);
            $yetkilisi = trim($row[3]);
            $borc_bakiye = floatval(str_replace(',', '.', $row[4]));
            $hesap_turu = trim($row[5]);
            $geciken_tutar = floatval(str_replace(',', '.', $row[6]));
            $acik_hesap_gunu = intval($row[7]);
            $gerc_vade = !empty($row[8]) ? date('Y-m-d', strtotime($row[8])) : null;
            $valoru = !empty($row[9]) ? date('Y-m-d', strtotime($row[9])) : null;
            $bakiye_odeme_tarihi = !empty($row[10]) ? date('Y-m-d', strtotime($row[10])) : null;
            $bilgi_kodu = trim($row[11]);
            $sube_kodu = trim($row[12]);

            if ($cari_kodu && $geciken_tutar > 0) {
                $stmt = $db->prepare("INSERT INTO vadesi_gecmis_borc (
                    cari_kodu, plasiyer, ticari_unvani, yetkilisi, borc_bakiye,
                    hesap_turu, geciken_tutar, acik_hesap_gunu, gerc_vade, valoru,
                    bakiye_odeme_tarihi, bilgi_kodu, sube_kodu
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $stmt->execute([
                    $cari_kodu, $plasiyer, $ticari_unvani, $yetkilisi, $borc_bakiye,
                    $hesap_turu, $geciken_tutar, $acik_hesap_gunu, $gerc_vade, $valoru,
                    $bakiye_odeme_tarihi, $bilgi_kodu, $sube_kodu
                ]);
                $sayac++;
            }
        }

        $mesaj = "$sayac kayıt başarıyla yüklendi.";
    }
}

$veriler = $database->fetchAll("SELECT * FROM vadesi_gecmis_borclar ");

?>
<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php $template->header(); ?>
        <!-- Content wrapper -->
        <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="row">
                    <div class="col-12 mt-2">
                        <div class="card">
                            <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Vadesi Geçmiş Borçlar</h5>
                            <div class="card-body">

                                <?php if (!empty($mesaj)) : ?>
                                    <div class="alert alert-success"><?php echo $mesaj; ?></div>
                                <?php endif; ?>

                                <!-- Temizleme ve Yükleme Formları -->
                                <form method="post" class="mb-3 d-flex justify-content-between" enctype="multipart/form-data">
                                    <div>
                                        <button type="submit" name="temizle" class="btn btn-danger" onclick="return confirm('Tüm kayıtlar silinecek. Emin misiniz?')">
                                            Veritabanını Temizle
                                        </button>
                                    </div>

                                    <div class="input-group w-50">
                                        <input type="file" name="excel" accept=".xlsx, .xls" required class="form-control">
                                        <button type="submit" name="yukle" class="btn btn-primary">Yükle</button>
                                    </div>
                                </form>

                                <!-- Tablo -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Cari Kodu</th>
                                                <th>Ticari Ünvanı</th>
                                                <th>Yetkili</th>
                                                <th>Geciken Tutar</th>
                                                <th>Gerçek Vade</th>
                                                <th>Valör</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($veriler as $veri): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($veri['cari_kodu']) ?></td>
                                                    <td><?= htmlspecialchars($veri['ticari_unvani']) ?></td>
                                                    <td><?= htmlspecialchars($veri['yetkilisi']) ?></td>
                                                    <td><?= number_format($veri['geciken_tutar'], 2, ',', '.') ?> ₺</td>
                                                    <td><?= $veri['gerc_vade'] ?></td>
                                                    <td><?= $veri['valoru'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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
</body>
</html>
