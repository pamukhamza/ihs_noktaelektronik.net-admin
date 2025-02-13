<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'kargo-firmalari';
$template = new Template('Kargo Firmaları - NEBSİS',  $currentPage);
// head'i çağırıyoruz
$template->head();
$database = new Database();

$kargo_id = $_GET['id'];
$kargo = $database->fetch("SELECT * FROM b2b_kargo WHERE id = $kargo_id ");

$zorunlu = "<span style='color: red;'>*</span>"
?>
<body>
<style>
    .form-group{margin-top:10px}
</style>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php $template->header(); ?>
        <!-- Content wrapper -->
        <div class="content-wrapper">
            <!-- Content -->
            <div class=" flex-grow-1 container-p-y container-xxl">
                <form action="functions/b2b/kargo/updateKargo.php" method="post">
                    <div class="row g-6">
                        <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Genel Bilgiler</h5>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="kargo_adi" >Kargo Adı <?= $zorunlu ?></label>
                                        <input type="text" name="kargo_id" id="kargo_id" hidden value="<?= $kargo['id']; ?>">
                                        <input id="kargo_adi" type="text" name="kargo_adi" required class="form-control form-control-sm-sm" value="<?= $kargo['kargo_adi'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="sorgu_linki" >Sorgu Linki</label>
                                        <input id="sorgu_linki" type="text" name="sorgu_linki" class="form-control form-control-sm-sm" value="<?= $kargo['sorgu_link'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="entegrasyon_firma" >Entegrasyon Firması</label>
                                        <select id="entegrasyon_firma" name="entegrasyon_firma" class="form-control form-control-sm">
                                            <option value="0" <?php echo ($kargo['entegrasyon_firma'] == '0' ? 'selected' : ''); ?>>Seçiniz</option>
                                            <option value="1" <?php echo ($kargo['entegrasyon_firma'] == '1' ? 'selected' : ''); ?>>Yurtiçi Kargo</option>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="k_adi_go" >Kullanıcı Adı (Gönderici Ödemeli)</label>
                                                <input id="k_adi_go" type="text" name="k_adi_go" class="form-control form-control-sm" value="<?= $kargo['kullanici_adi_go'] ?>">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="parola_go" >Parola (Gönderici Ödemeli)</label>
                                                <input id="parola_go" type="text" name="parola_go" class="form-control form-control-sm" value="<?= $kargo['parola_go'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="k_adi_ao" >Kullanıcı Adı (Alıcı Ödemeli)</label>
                                                <input id="k_adi_ao" type="text" name="k_adi_ao" class="form-control form-control-sm" value="<?= $kargo['kullanici_adi_ao'] ?>">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="parola_ao" >Parola (Alıcı Ödemeli)</label>
                                                <input id="parola_ao" type="text" name="parola_ao" class="form-control form-control-sm" value="<?= $kargo['parola_ao'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" >
                                        <label for="sabit_kargo_ucreti" >Sabit Kargo Ücreti</label>
                                        <input id="sabit_kargo_ucreti" type="text" name="sabit_kargo_ucreti" class="form-control form-control-sm" value="<?= $kargo['sabit_kargo_ucreti'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <div class="checkbox ml-4">
                                            <input type="checkbox" id="ucretsiz_kargo_devre_disi" name="ucretsiz_kargo_devre_disi" class="form-check-input" <?php echo ($kargo['ucretsiz_kargo_devre_disi'] == 1 ? 'checked' : ''); ?>/>
                                            <label for="ucretsiz_kargo_devre_disi">Ücretsiz Kargo Devre Dışı</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="kargo_vergi_no" >Kargo Firmasi Vergi Numarası</label>
                                        <input id="kargo_vergi_no" type="text" name="kargo_vergi_no" class="form-control form-control-sm" value="<?= $kargo['kargo_firma_vergi_no'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="aktif" >Yayın Durumu</label>
                                        <select id="aktif" name="aktif" class="form-control form-control-sm">
                                            <option value="1" <?php echo ($kargo['yayin_durumu'] == '1' ? 'selected' : ''); ?>>Yayında</option>
                                            <option value="0" <?php echo ($kargo['yayin_durumu'] == '0' ? 'selected' : ''); ?>>Yayında Değil</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Gösterim</h5>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="gosterim">Gösterim</label>
                                        <select id="gosterim" name="gosterim" class="form-control form-control-sm">
                                            <option value="1" <?php echo ($kargo['gosterim'] == '1' ? 'selected' : ''); ?>>
                                                Müşteri ve Bayi (B2C) - (B2B)
                                            </option>
                                            <option value="2" <?php echo ($kargo['gosterim'] == '2' ? 'selected' : ''); ?>>
                                                Müşteri (B2C)
                                            </option>
                                            <option value="3" <?php echo ($kargo['gosterim'] == '3' ? 'selected' : ''); ?>>
                                                Bayi (B2B)
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="minimum_gosterim_tutari">Minimum Gösterim Tutarı</label>
                                        <input id="minimum_gosterim_tutari" name="minimum_gosterim_tutari" type="text" class="form-control form-control-sm" value="<?= $kargo['minimum_gosterim_tutari'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="maksimum_gosterim_tutari">Maksimum Gösterim Tutarı</label>
                                        <input id="maksimum_gosterim_tutari" name="maksimum_gosterim_tutari" type="text" class="form-control form-control-sm" value="<?= $kargo['maksimum_gosterim_tutari'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="maksimum_desi_miktari">Maksimum Desi Miktarı</label>
                                        <input id="maksimum_desi_miktari" name="maksimum_desi_miktari" type="text" class="form-control form-control-sm" value="<?= $kargo['maksimum_desi_miktari'] ?>">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-12 pl-0">
                            <p class="text-center">
                                <button type="submit" name="kargo_guncelle" class="btn btn-space btn-primary font-weight-bold">Kaydet</button>
                            </p>
                        </div>
                    </div>
                </form>
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
<script src="assets/js/app.js"></script>
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
