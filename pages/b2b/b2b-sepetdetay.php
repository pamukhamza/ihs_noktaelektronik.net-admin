<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'b2b-sepetler';
$template = new Template('Sepet Detay - NEBSİS',  $currentPage);
// head'i çağırıyoruz
$template->head();
$database = new Database();

$uye_id = $_GET['id'];
$uye = $database->fetch("SELECT fiyat, firmaUnvani, ad, soyad FROM uyeler WHERE id = $uye_id");
$uye_fiyat = $uye['fiyat'];

$sepetler = $database->fetchAll(" SELECT s.*, u.UrunKodu, u.UrunAdiTR, u.DSF1, u.DSF{$uye_fiyat}, u.KSF{$uye_fiyat}, u.DOVIZ_BIRIMI, u.kdv,
           (SELECT KResim FROM nokta_urunler_resimler WHERE urun_id = u.BLKODU LIMIT 1) as foto
                FROM uye_sepet s
                JOIN nokta_urunler u ON s.urun_id = u.id
                WHERE s.uye_id = $uye_id ");

$firstDate = $lastDate = null; // Initialize variables
if (!empty($sepetler)) {
    $lastDate = end($sepetler)['tarih'];
    $lastindirim = end($sepetler)['sepet_ozel_indirim'];
}
$totalKdvliFiyat = 0;


$dolar = $database->fetch("SELECT satis FROM b2b_kurlar WHERE id = 2 ");
$satis_dolar_kuru = $dolar['satis'];
$euro = $database->fetch("SELECT satis FROM b2b_kurlar WHERE id = 3 ");
$satis_euro_kuru = $euro['satis'];

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
                <div class="row g-6">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <p class="">
                            <button type="submit" class="edit-sepet btn btn-space btn-primary font-weight-bold">Sepete Ürün Ekle</button>
                        </p>
                        <div class="card">
                            <div class="card-header">
                                <h2><b><?php echo $uye['firmaUnvani'] ?></b></h2>
                                <h3><?php echo $uye['ad'] .' '.  $uye['soyad']?></h3>
                                <p><?php echo isset($lastDate) ? " $lastDate" : ''; ?></p>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <th>Resim</th>
                                        <th>Stok Kodu</th>
                                        <th>Ürün</th>
                                        <th>Miktar</th>
                                        <th>Özel Fiyat</th>
                                        <th>Üyenin <br>Gördüğü Fiyat</th>
                                        <th>KDV Dahil <br> Toplam Fiyat</th>
                                        <th>KDV Dahil KPB <br> Toplam Fiyat</th>
                                        <th>Sil</th>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($sepetler as $sepet): ?>
                                        <tr>
                                            <?php
                                                $urun_id = $sepet['urun_id'];
                                                $urun = $database->fetch("SELECT BLKODU, seo_link, UrunKodu, UrunAdiTR, DSF1, DSF2, DSF3, DSF4, KSF1, KSF2, KSF3, KSF4, DOVIZ_BIRIMI, kdv FROM nokta_urunler WHERE id =$urun_id");
                                                $urunBLKODU = $urun['BLKODU'];

                                                $imagesQuery = "SELECT DISTINCT KResim FROM nokta_urunler_resimler WHERE UrunID = :product_id ORDER BY Sira ASC ";
                                                $imageParams = ['product_id' => $urun_id];
                                                $image = $database->fetch($imagesQuery, $imageParams);
                                                
                                            ?>
                                            <td><a target="_blank" href="tr/urun/<?= $urun['seo_link'] ?>"><img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/<?= $image['KResim']; ?>" style="max-width: 50px; max-height: 50px;"></a></td>
                                            <td><?= $urun['UrunKodu']; ?></td>
                                            <td><?= $urun['UrunAdiTR']; ?></td>
                                            <td><input type="text" class="form-control adet-input" value="<?= $sepet['adet']; ?>" data-sepet-id="<?= $sepet['id']; ?>"></td>
                                            <td>
                                                <input type="text" class="form-control fiyat-input" value="<?= $sepet['ozel_fiyat']; ?>" data-sepet-id="<?= $sepet['id']; ?>" data-dsf1="<?= $urun['DSF1']; ?>">
                                            </td>
                                            <td>
                                                <?php
                                                $uyenin_fiyati = !empty($urun["DSF".$uye_fiyat]) ? $urun["DSF".$uye_fiyat] : $urun["KSF".$uye_fiyat];
                                                echo number_format($uyenin_fiyati, 2, ',', '.');
                                                echo !empty($urun["DSF4"]) ? $urun["DOVIZ_BIRIMI"] : "₺"; ?>
                                                <!--//" data-sepet-id="<?php $sepet['id']; ?>"> -->
                                            </td>
                                            <td>
                                                <?php
                                                $kdvOrani = is_numeric($urun['kdv']) ? $urun['kdv'] / 100 : 0;
                                                $secilen_fiyat = !empty($sepet['ozel_fiyat']) ? $sepet['ozel_fiyat'] : $uyenin_fiyati;
                                                $kdvli = is_numeric($secilen_fiyat) ? $sepet['adet'] * $secilen_fiyat * (1 + $kdvOrani) : 0;
                                                echo number_format($kdvli, 2, ',', '.');
                                                echo !empty($urun["DSF4"]) ? $urun["DOVIZ_BIRIMI"] : "₺";
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (!empty($urun["DSF4"])) {
                                                    if ($urun["DOVIZ_BIRIMI"] == "$") {
                                                        $fiyat123 = $satis_dolar_kuru * $kdvli;
                                                    } elseif ($urun["DOVIZ_BIRIMI"] == "€") {
                                                        $fiyat123 = $satis_euro_kuru * $kdvli;
                                                    }
                                                }else{
                                                    $fiyat123 = $kdvli;
                                                }
                                                $totalKdvliFiyat += $fiyat123;
                                                echo number_format($fiyat123, 2, ',', '.');
                                                echo '₺';
                                                ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-light" onclick="SepetSil(<?php echo $sepet['id']?>)"><i class="fa-solid fa-trash-can"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="8"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="no-border"></td> <!-- Diğer hücre sayısına göre colspan değeri ayarlanmalı -->
                                        <td><b>Toplam KDV Dahil Fiyat:</b></td>
                                        <td><?php
                                            echo number_format($totalKdvliFiyat, 2, ',', '.');
                                            echo '₺';
                                            ?>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="no-border"></td> <!-- Diğer hücre sayısına göre colspan değeri ayarlanmalı -->
                                        <td><b>Toplam KDV Dahil $ Fiyat:</b></td>
                                        <td><?php
                                            $tpldlrfyt = $totalKdvliFiyat / $satis_dolar_kuru;
                                            echo number_format($tpldlrfyt, 2, ',', '.');
                                            echo '$';
                                            ?>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="no-border"></td> <!-- Diğer hücre sayısına göre colspan değeri ayarlanmalı -->
                                        <td><b>Toplam KDV Dahil € Fiyat:</b></td>
                                        <td><?php
                                            $tpleurofyt = $totalKdvliFiyat / $satis_euro_kuru;
                                            echo number_format($tpleurofyt, 2, ',', '.');
                                            echo '€';
                                            ?>
                                        </td>
                                        <td></td>
                                    </tr>
                                        <tr>
                                            <td colspan="6"></td> <!-- Diğer hücre sayısına göre colspan değeri ayarlanmalı -->
                                            <td><b>Sepet Özel İndirim:</b></td>
                                            <td>
                                                <?php
                                                $sepetOzelIndirim = !empty($lastindirim) ? $lastindirim : '';
                                                ?>
                                                <input type="text" class="form-control indirim-input" value="<?php echo $sepetOzelIndirim; ?>" data-uye-id="<?php echo $uye_id; ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6"></td> <!-- Diğer hücre sayısına göre colspan değeri ayarlanmalı -->
                                            <td><b>Sepet Toplamı:</b></td>
                                            <td><?php
                                                // Convert $sepetOzelIndirim to float before subtraction
                                                $sepetToplam = $totalKdvliFiyat - (float)$sepetOzelIndirim;
                                                echo $sepetToplam; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
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


<!-- Edit User Modal -->
<div class="modal fade" id="editSepetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <div class="modal-body">
                <form id="editSliderForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="editAdet">Adet</label>
                        <input type="hidden" class="form-control" id="editUyeId" name="editUyeId" value="<?= $uye_id ;?>">
                        <input type="number" class="form-control" id="editAdet" name="editAdet" value="1">
                    </div>
                    <div class="form-group">
                        <label for="editUrun">Ürün Seç</label>
                        <select style="width: 100%" class="form-control select1" name="editUrun" id="editUrun">
                            <?php
                                $uruns = $database->fetchAll("SELECT id, UrunKodu FROM nokta_urunler WHERE BLKODU != 0 ");
                                foreach($uruns as $row) {
                            ?>
                                <option value='<?= $row['id']; ?>'><?= $row['UrunKodu']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveSepet">Kaydet</button>
            </div>
        </div>
    </div>
</div>
<!--/ Edit User Modal -->

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
    $(document).ready(function() {
        var isUploading = false;
        $(document).on('click', '.edit-sepet', function() {
            $('#editSepetModal').modal('show');
        });
        $('#saveSepet').click(function() {
            if (isUploading) {
                return;
            }
            isUploading = true;
            var uye_id = $('#editUyeId').val();
            var urun_adet = $('#editAdet').val();
            var urun_id = $('#editUrun').val();
            var formData = new FormData();
            formData.append('uye_id', uye_id);
            formData.append('urun_adet', urun_adet);
            formData.append('urun_id', urun_id);
            formData.append('type', 'admin_sepete_urun_ekle');
            $.ajax({
                url: 'functions/b2b/muhasebe/edit_sepet.php',
                method: 'post',
                data: formData,
                processData: false, // Important! Don't process data, allows FormData to handle it
                contentType: false, // Important! Don't set contentType
                success: function() {
                    // Close the edit modal
                    $('#editSepetModal').modal('hide');
                    // Refresh the slider list
                    isUploading = false;
                    location.reload();
                }
            });
        });
        $('.indirim-input').on('blur', function() {
            var uyeId = $(this).data('uye-id');
            var indirimValue = $('.indirim-input[data-uye-id="'+uyeId+'"]').val();
            $.ajax({
                url: 'functions/b2b/muhasebe/edit_sepet.php',
                type: 'POST',
                data: {
                    uyeId: uyeId,
                    indirim: indirimValue,
                    type: 'anlik_sepet_indirim'
                },
                success: function(response) {
                    // Başarılı yanıtın ardından gerekirse ek işlemler
                    console.log(response);
                    window.location.href = 'pages/b2b/b2b-sepetdetay?id=<?= $uye_id; ?>&s=1';
                },
                error: function(error) {
                    console.error(error);
                }
            });
        });
        $('.adet-input, .fiyat-input').on('blur', function() {
            var sepetId = $(this).data('sepet-id');
            var adetValue = $('.adet-input[data-sepet-id="'+sepetId+'"]').val();
            var fiyatInput = $('.fiyat-input[data-sepet-id="'+sepetId+'"]');
            var fiyatValue = fiyatInput.val().replace(',', '.'); // Virgülleri noktaya dönüştürdük
            fiyatValue = fiyatValue === '' ? null : parseFloat(fiyatValue); // Boşsa null yapıyoruz
            var dsf1Value = parseFloat(fiyatInput.data('dsf1')); // DSF1 değerini alıyoruz
            // Boş olmadığını kontrol etmek ve gerekli uyarıyı yapmak
            if (!isNaN(fiyatValue) && !isNaN(dsf1Value) && (fiyatValue < dsf1Value) && fiyatValue != null) {
                alert('Fiyat değeri minimum ' + dsf1Value + ' olmalıdır.');
                fiyatInput.val(''); // fiyat alanını boş olarak ayarlar
                fiyatValue = null; // boş olduğunda null yapıyoruz
            }
            $.ajax({
                url: 'functions/b2b/muhasebe/edit_sepet.php', // AJAX işlemini gerçekleştirecek PHP dosyanızın adı
                type: 'POST',
                data: {
                    sepetId: sepetId,
                    adet: adetValue,
                    fiyat: fiyatValue,
                    type: 'anlik_sepet_guncelle'
                },
                success: function(response) {
                    // Başarılı yanıtın ardından gerekirse ek işlemler
                    window.location.href = 'pages/b2b/b2b-sepetdetay?id=<?php echo $uye_id ?>&s=1';
                    console.log(response);
                },
                error: function(error) {
                    console.error(error);
                }
            });
        });
    });
    function SepetSil(gid) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: 'Bu eylem geri alınamaz!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Evet',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'functions/b2b/muhasebe/edit_sepet.php',
                        type: 'POST',
                        data: {
                            'gid': gid,
                            type: 'sepet_urun_sil'
                        },
                        success: function () {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'Sepetteki Ürün Kaldırıldı!' /* Ürün Silindi! */,
                                showConfirmButton: false,
                                timer: 1000
                            });
                            setTimeout(function () {
                                window.location.href = 'pages/b2b/b2b-sepetdetay?id=<?php echo $uye_id ?>';
                            }, 1000);
                        }
                    });
                }
            });
        }
</script>
</body>
</html>