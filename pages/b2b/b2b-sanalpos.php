<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'b2b-sanal-pos-odeme';
$template = new Template('Banka Hesap Bilgileri - NEBSİS',  $currentPage);
// head'i çağırıyoruz
$template->head();
$database = new Database();
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
                    
                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                        <div class="card">
                            <form method="post" action="" id="paymentForm">
                                <input type="hidden" name="adminCariOdeme" value="">
                                <input type="hidden" name="taksit_sayisi" value="">
                                <input type="hidden" name="lang" value="tr">
                                <div class="card-body">
                                    <div class="text-center rounded" style="background-color: #0a90eb;"><h4 class="card-title font-weight-bold" style="color: whitesmoke">Tüm Poslar</h4></div>
                                    <small class="text-danger">İşlem sırası Komisyon > Hesaba İşlenecek Tutar > Diğerleri !</small>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="uye_parola">İşlenecek Cari</label>
                                                <select class="form-control" id="uye_id" name="uye_id" style="width:100%" readonly>
                                                    <option value='0'>Firma Seç</option>
                                                    <?php
                                                    $uyeler = $database->fetchAll("SELECT muhasebe_kodu, id, firmaUnvani FROM uyeler");
                                                    foreach($uyeler as $row) {
                                                        ?>
                                                        <option value='<?php echo $row['id']; ?>'><?php echo $row['muhasebe_kodu']. ' - ' . $row['firmaUnvani']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 ">
                                            <label for="tutarInput">Hesaba İşlenecek Tutar <small class="text-danger">(Virgül yerine nokta kullan!)</small></label>
                                            <input type="text" class="form-control" id="toplam" name="toplam" placeholder="Ör. 1582.44" required>
                                        </div>
                                        <div class="col-md-6 ">
                                            <label for="hesap">Müşteri Hesabı</label>
                                            <select class="form-control" name="hesap">
                                                <option value="0">TL Hesabıma İşle</option>
                                                <option value="1">Döviz Hesabıma İşle</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="banka_id">Wolvox Banka Hesabı</label>
                                                <select class="form-control" id="banka_id" name="banka_id" style="width:100%" readonly>
                                                    <option value='0'>Banka Tanımı Seçiniz</option>
                                                    <?php
                                                    $bankalar = $database->fetchAll("SELECT * FROM b2b_banka_pos_listesi");
                                                    foreach($bankalar as $row) {
                                                        ?>
                                                        <option value='<?php echo $row['id']; ?>'><?php echo $row['id']. ' - ' . $row['BANKA_ADI'] . ' - ' . $row['TANIMI'] . ' - Taksit Sayısı: ' .  $row['TAKSIT_SAYISI']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="cardName">Kart Sahibi</label>
                                            <input type="text" class="form-control" id="cardName" name="cardName" placeholder="" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cardNumber">Kart Numarası</label>
                                            <input type="text" class="form-control" id="cardNumber" MAXLENGTH="16" name="cardNumber" placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6">
                                            <label class="form-label" >SKT</label>
                                            <div class="d-flex">
                                                <input type="text" id="expMonth" name="expMonth" class="form-control me-2" placeholder="Ay" autocomplete="off" required MAXLENGTH="3" />
                                                <input type="text" id="expYear" name="expYear" class="form-control" placeholder="Yıl" autocomplete="off" required MAXLENGTH="3" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="cvCode">CVV</label>
                                            <input type="text" class="form-control" id="cvCode" name="cvCode" MAXLENGTH="4" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <small id="kart-gelen-bilgi"></small>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="d-block my-3 col-6 col-sm-6 col-md-6">
                                            <div class="custom-control custom-radio">
                                                <input id="paramPos" name="paymentMethod" type="radio" class="custom-control-input" value="1" checked required>
                                                <label class="custom-control-label" for="paramPos">Param Pos</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input id="kuveytPos" name="paymentMethod" type="radio" class="custom-control-input" value="3" required>
                                                <label class="custom-control-label" for="kuveytPos">Kuveyt Pos</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input id="turkiyeFinansPos" name="paymentMethod" type="radio" class="custom-control-input" value="4" required>
                                                <label class="custom-control-label" for="turkiyeFinansPos">Türkiye Finans Pos</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-6 col-md-6 row">
                                            <div class="col-6 mb-3">
                                                <label for="odemetaksit">Taksit</label>
                                                <input type="text" class="form-control" id="odemetaksit" name="odemetaksit" placeholder="Ör. 2" value="1" required>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="vade">Komisyon</label>
                                                <input type="text" class="form-control" id="vade" name="vade" placeholder="Ör. 4.97" value="0" required>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label for="odemetutar">Çekilecek Tutar</label>
                                                <input type="text" class="form-control" id="odemetutar" name="odemetutar" placeholder="Ör. 4.97" required>
                                            </div>
                                        </div>
                                        <input type="hidden" name="tip" id="tip" value="Sanal Pos">
                                    </div>
                                    <hr>
                                    <div class="text-center">
                                        <button type="submit" id="kartlaOdemeyeGec" name="kartlaOdemeyeGec" class="btn btn-space btn-primary">Gönder</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                        <div class="card">
                            <form method="post" action="../../php/bank/turkiye_finans/request.php">
                                <input type="hidden" name="adminCariOdeme" value="">
                                <input type="hidden" name="taksit_sayisi" value="">
                                <input type="hidden" name="lang" value="tr">
                                <div class="card-body">
                                    <div class="text-center rounded" style="background-color: #0a90eb;"><h4 class="card-title font-weight-bold" style="color: whitesmoke">Türkiye Finans - 3 Taksit Çekim</h4></div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="uye_parola">İşlenecek Cari</label>
                                                <select class="form-control" id="uye_id1" name="uye_id" style="width:100%" readonly>
                                                    <option value='0'>Firma Seç</option>
                                                    <?php
                                                    $uyeler = $database->fetchAll("SELECT muhasebe_kodu, id, firmaUnvani FROM uyeler");

                                                    foreach($uyeler as $row) {
                                                        ?>
                                                        <option value='<?php echo $row['id']; ?>'><?php echo $row['muhasebe_kodu']. ' - ' . $row['firmaUnvani']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 ">
                                            <label for="tutarInput">Hesaba İşlenecek Tutar <small class="text-danger">(Virgül yerine nokta kullan!)</small></label>
                                            <input type="text" class="form-control" id="toplam1" name="toplam" placeholder="Ör. 1582.44" required>
                                        </div>
                                        <div class="col-md-6 ">
                                            <label for="hesap">Müşteri Hesabı</label>
                                            <select class="form-control" name="hesap">
                                                <option value="0">TL Hesabıma İşle</option>
                                                <option value="1">Döviz Hesabıma İşle</option>
                                            </select>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="cardName">Kart Sahibi</label>
                                            <input type="text" class="form-control" id="cardName" name="cardName" placeholder="" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cardNumber">Kart Numarası</label>
                                            <input type="text" class="form-control" id="cardNumber" MAXLENGTH="16" name="cardNumber" placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class=" col-sm-12 col-md-6">
                                            <label class="form-label" >SKT</label>
                                            <div class="d-flex">
                                                <input type="text" id="expMonth" name="expMonth" class="form-control me-2" placeholder="Ay" autocomplete="off" required MAXLENGTH="3" />
                                                <input type="text" id="expYear" name="expYear" class="form-control" placeholder="Yıl" autocomplete="off" required MAXLENGTH="3" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="cvCode">CVV</label>
                                            <input type="text" class="form-control" id="cvCode" name="cvCode" MAXLENGTH="4" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <small id="kart-gelen-bilgi"></small>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <input type="hidden" name="odemetutar" id="odemetutar1">
                                        <input type="hidden" name="odemetaksit" id="odemetaksit" value="3">
                                        <input type="hidden" name="tip" id="tip" value="Sanal Pos">
                                        <input type="hidden" name="vade" id="vade" value="0">
                                        <input type="hidden" name="banka_id" id="banka_id" value="59">
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" id="kartlaOdemeyeGec" name="kartlaOdemeyeGec" class="btn btn-space btn-primary">Gönder</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card mt-5">
                            <form method="post" action="../../php/bank/turkiye_finans/request.php">
                                <input type="hidden" name="adminCariOdeme" value="">
                                <input type="hidden" name="taksit_sayisi" value="">
                                <input type="hidden" name="lang" value="tr">
                                <div class="card-body">
                                    <div class="text-center rounded" style="background-color: #0a90eb;"><h4 class="card-title font-weight-bold" style="color: whitesmoke">Türkiye Finans - Tek Çekim</h4></div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="uye_parola">İşlenecek Cari</label>
                                                <select class="form-control" id="uye_id2" name="uye_id" style="width:100%" readonly>
                                                    <option value='0'>Firma Seç</option>
                                                    <?php
                                                    $uyeler = $database->fetchAll("SELECT muhasebe_kodu, id, firmaUnvani FROM uyeler");
                                                    foreach($uyeler as $row) {
                                                        ?>
                                                        <option value='<?php echo $row['id']; ?>'><?php echo $row['muhasebe_kodu']. ' - ' . $row['firmaUnvani']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 ">
                                            <label for="tutarInput">Hesaba İşlenecek Tutar <small class="text-danger">(Virgül yerine nokta kullan!)</small></label>
                                            <input type="text" class="form-control" id="toplam2" name="toplam" placeholder="Ör. 1582.44" required>
                                        </div>
                                        <div class="col-md-6 ">
                                            <label for="hesap">Müşteri Hesabı</label>
                                            <select class="form-control" name="hesap">
                                                <option value="0">TL Hesabıma İşle</option>
                                                <option value="1">Döviz Hesabıma İşle</option>
                                            </select>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="cardName">Kart Sahibi</label>
                                            <input type="text" class="form-control" id="cardName" name="cardName" placeholder="" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cardNumber">Kart Numarası</label>
                                            <input type="text" class="form-control" id="cardNumber" MAXLENGTH="16" name="cardNumber" placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class=" col-sm-12 col-md-6">
                                            <label class="form-label" >SKT</label>
                                            <div class="d-flex">
                                                <input type="text" id="expMonth" name="expMonth" class="form-control me-2" placeholder="Ay" autocomplete="off" required MAXLENGTH="3" />
                                                <input type="text" id="expYear" name="expYear" class="form-control" placeholder="Yıl" autocomplete="off" required MAXLENGTH="3" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="cvCode">CVV</label>
                                            <input type="text" class="form-control" id="cvCode" name="cvCode" MAXLENGTH="4" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <small id="kart-gelen-bilgi"></small>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <input type="hidden" name="odemetutar" id="odemetutar2">
                                        <input type="hidden" name="odemetaksit" id="odemetaksit" value="1">
                                        <input type="hidden" name="tip" id="tip" value="Sanal Pos">
                                        <input type="hidden" name="vade" id="vade" value="0">
                                        <input type="hidden" name="banka_id" id="banka_id" value="57">
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" id="kartlaOdemeyeGec" name="kartlaOdemeyeGec" class="btn btn-space btn-primary">Gönder</button>
                                    </div>
                                </div>
                            </form>
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
<script src="assets/js/app.js"></script>



<!-- Edit User Modal -->
<div class="modal fade" id="editBankaBilgisiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2">Banka Bilgileri</h4>
                </div>
                <form id="editBankaBilgisiForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="editBankaAdi">Banka Adı</label>
                        <input type="text" hidden id="editBankaBilgisiId" name="editBankaBilgisiId">
                        <input type="text" class="form-control" id="editBankaAdi" name="editBankaAdi" >
                    </div>
                    <div class="form-group">
                        <label for="editSubeAdi">Şube Adı</label>
                        <input type="text" class="form-control" id="editSubeAdi" name="editSubeAdi" >
                    </div>
                    <div class="form-group">
                        <label for="editIban">IBAN</label>
                        <input type="text" class="form-control" id="editIban" name="editIban" >
                    </div>
                    <div class="form-group">
                        <label for="editKolayAdres">Kolay Adres</label>
                        <input type="text" class="form-control" id="editKolayAdres" name="editKolayAdres" >
                    </div>
                    <div class="form-group">
                        <label for="editHesapSahibi">Hesap Sahibi</label>
                        <input type="text" class="form-control" id="editHesapSahibi" name="editHesapSahibi" >
                    </div>
                    <div class="form-group">
                        <label for="editHesapTuru">Hesap Türü</label>
                        <select class="form-control" id="editHesapTuru" name="editHesapTuru">
                            <option value="TÜRK LİRASI">TÜRK LİRASI</option>
                            <option value="USD">USD</option>
                            <option value="EURO">EURO</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editSwift">Swift Kodu</label>
                        <input type="text" class="form-control" id="editSwift" name="editSwift" >
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" id="saveEditBankaBilgisi" class="btn btn-primary me-3">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Edit User Modal -->
</body>
</html>
<script>
$(document).ready(function() {
    $('#cardNumber').on('input', function() {
        kartBinSorgulama('#cardNumber', 'kart-gelen-bilgi');
    });
});
</script>