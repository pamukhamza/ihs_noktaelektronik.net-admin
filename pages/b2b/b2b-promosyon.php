<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'b2b-promosyon';
$template = new Template('Promosyonlar - NEBSİS', $currentPage);
$template->head();
$database = new Database();

?>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php $template->header(); ?>
            <div class="container dashboard-content ">
                <!-- end pageheader  -->
                <div class="ecommerce-widget">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mt-5">
                            <div class="card">
                                <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Özel Promosyon Kodu Oluştur</h5>
                                <span style="color: red" class="pt-2 pl-2">*Filtrelerde tümünü seçmek için boş bırakınız.</span>
                                <div class="card-body">
                                    <form id="promosyonForm">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="indirim_miktari">Indirim Miktarı:</label>
                                                <input type="number" class="form-control" id="indirim_miktari" name="indirim_miktari" placeholder="Örn: 25" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="min_sepet">Minimum Sepet Tutarı:</label>
                                                <input type="number" class="form-control" id="min_sepet" name="min_sepet" placeholder="Örn: 25" required>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="urun">Ürün:</label>
                                                <select class="form-control" id="keep-order1" name="urun[]" multiple>
                                                    <option value=''>Ürün seçiniz</option>
                                                    <?php
                                                    $q = "SELECT id, UrunKodu, UrunAdiTR FROM nokta_urunler";
                                                    $rows = $database->fetchAll($q);
                                                    foreach ($rows as $k => $row) { ?>
                                                        <option value='<?= $row['id'] ?>'><?= $row['UrunKodu'] ?> - <?= $row['UrunAdiTR'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="max_kul">Maksimum kaç üye kullanabilir:</label>
                                                <input type="number" class="form-control" id="max_kul" name="max_kul" value="1" required>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="kategori">Kategori:</label>
                                                <select class="form-control" id="keep-order2" name="kategori[]" multiple>
                                                    <option value=''>Kategori seçiniz</option>
                                                    <?php
                                                    $q = "SELECT * FROM nokta_kategoriler";
                                                    $rows = $database->fetchAll($q);
                                                    foreach ($rows as $k => $row) { ?>
                                                        <option value='<?= $row['id'] ?>'><?= $row['KategoriAdiTr'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="marka">Marka:</label>
                                                <select class="form-control" id="keep-order3" name="marka[]" multiple>
                                                    <option value=''>Marka seçiniz</option>
                                                    <?php
                                                    $q = "SELECT * FROM nokta_urun_markalar WHERE is_active = 1";
                                                    $rows = $database->fetchAll($q);
                                                    foreach ($rows as $k => $row) { ?>
                                                        <option value='<?= $row['id'] ?>'><?= $row['title'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="uyeler">Üyeler</label>
                                            <select id='keep-order' name="uyeler[]" multiple style="width: 100%;">
                                                <?php
                                                $q = "SELECT * FROM uyeler";
                                                $brands = $database->fetchAll($q);
                                                ?>
                                                <option value=''>Tüm Üyeler</option>
                                                <?php foreach($brands as $row) { ?>
                                                    <option value='<?php echo $row['id']; ?>'><?php echo $row['firmaUnvani']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="baslik">Kupon Başlığı:</label>
                                                <input type="text" class="form-control" id="baslik" name="baslik"  required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="aciklama">Açıklama:</label>
                                                <input type="text" class="form-control" id="aciklama" name="aciklama" required>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="date">Bitiş Tarihi</label>
                                                <input type="date" class="form-control" id="date" name="date">
                                            </div>
                                        </div>
                                        <input type="hidden" id="promosyon_ols" name="promosyon_ols" value="promosyon_ols">
                                        <button type="submit" name="promosyon_olustur" class="btn btn-primary mt-5">Promosyon Kodu Oluştur</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12 mt-5">
                            <div class="card">
                                <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Yeni Promosyon Kodu</h5>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="promosyon_kodu">Yeni Promosyon Kodu</label>
                                        <input type="text" class="form-control " id="promosyon_kodu" name="promosyon_kodu" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12 mt-5">
                            <div class="card">
                                <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Promosyon Kodları</h5>
                                <div class="table-responsive">
                                        <table class="table table-bordered w-100">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Promosyon Kodu</th>
                                                    <th>Kullanan Üyeler</th>
                                                    <th>Tutar</th>
                                                    <th>Min. Sepet Tutarı</th>
                                                    <th>Ürün Stok Kodu</th>
                                                    <th>Max. Kullanıcı Sayısı</th>
                                                    <th>Kullanım Sayısı</th>
                                                    <th>Tarih</th>
                                                    <th>Kullanım</th>
                                                    <th>Aktif/Pasif</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $q = "SELECT * FROM b2b_promosyon ORDER BY id DESC";
                                                $rows = $database->fetchAll($q);
                                                if ($rows) {
                                                    foreach ($rows as $k => $row) {
                                                        // Ürün kodunu almak için
                                                        $urunKodu = "";
                                                        if (!empty($urunId)) {
                                                            $q = "SELECT UrunKodu FROM nokta_urunler WHERE id = :urun_id";
                                                            $params = [
                                                                'urun_id' => $urunId
                                                            ];
                                                            $urunResult = $database->fetch($q, $params);
                                                            if ($urunResult) {
                                                                $urunKodu = $urunResult["UrunKodu"];
                                                            }
                                                        }
                                                ?>
                                                        <tr class="border">
                                                            <td><?= $row["id"]; ?></td>
                                                            <td><?= $row["promosyon_kodu"]; ?></td>
                                                            <td><a href="admin/siparisler/promosyon_kullananlar?id=<?= $row["id"] ?>">Kullananları Gör</a></td>
                                                            <td><?= $row["tutar"]; ?>₺</td>
                                                            <td><?= $row["minSepetTutar"]; ?>₺</td>
                                                            <td><?= $urunKodu; ?></td>
                                                            <td><?= $row["max_kullanim_sayisi"]; ?></td>
                                                            <td><?= $row["kullanim_sayisi"]; ?></td>
                                                            <td><?= $row["tarih"]; ?></td>
                                                            <td><?= ($row["kullanildi"] == 1) ? "Kullanıldı" : "Kullanılmadı"; ?></td>
                                                            <td>
                                                                <label class="switch switch-success">
                                                                    <input type="checkbox" class="switch-input active-checkbox-slider" data-id="<?= $row['id']; ?>" <?= $row['aktif'] == 1 ? 'checked' : ''; ?> />
                                                                    <span class="switch-toggle-slider">
                                                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                                                    </span>
                                                                    <span class="switch-label"></span>
                                                                </label>
                                                            </td>
                                                        </tr>
                                                <?php } } ?>
                                            </tbody>
                                        </table>
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
<!-- AJAX ile Promosyon Kodu Oluşturma ve Kullanma İşlemleri -->
<script>
    $(document).ready(function() {
        $('#promosyonForm').on('submit', function(e) {
            e.preventDefault(); // Formun normal gönderilmesini engeller
            var formData = $(this).serialize(); // Form verilerini alır
            $.ajax({
                type: 'POST',
                url: 'functions/siparisler/promosyon.php', // function.php dosyasına POST isteği gönderiliyor
                data: formData,
                success: function(response) {
                    $('#promosyon_kodu').val(response); // Gelen yanıtı input alanına yazar
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.log(xhr.responseText);
                }
            });
        });
    });

$(document).ready(function() {
    $('#keep-order').select2();
    $('#keep-order1').select2();
    $('#keep-order2').select2();
    $('#keep-order3').select2();
    $('#search-filter').on('keyup', function() {
        $('#keep-order').val(null).trigger('change'); // Reset the selected values
        var searchText = $(this).val().toLowerCase();
        $('#keep-order option').each(function() {
            var optionText = $(this).text().toLowerCase();
            if (optionText.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    $('#search-filter').on('keyup', function() {
        $('#keep-order1').val(null).trigger('change'); // Reset the selected values
        var searchText = $(this).val().toLowerCase();
        $('#keep-order1 option').each(function() {
            var optionText = $(this).text().toLowerCase();
            if (optionText.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    $('#search-filter').on('keyup', function() {
        $('#keep-order2').val(null).trigger('change'); // Reset the selected values
        var searchText = $(this).val().toLowerCase();
        $('#keep-order2 option').each(function() {
            var optionText = $(this).text().toLowerCase();
            if (optionText.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    $('#search-filter').on('keyup', function() {
        $('#keep-order3').val(null).trigger('change'); // Reset the selected values
        var searchText = $(this).val().toLowerCase();
        $('#keep-order3 option').each(function() {
            var optionText = $(this).text().toLowerCase();
            if (optionText.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
$('.active-checkbox-slider').on('change', function() {
        var id = $(this).data('id');
        var activeStatus = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: 'functions/update_status.php',  // PHP dosyanızın ismini yazın
            type: 'POST',
            data: {
                id: id,
                field: 'aktif',
                value: activeStatus,
                database: 'b2b_promosyon'
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    text: response,
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function() {
                alert('Error while updating');
            }
        });
    });
</script>
