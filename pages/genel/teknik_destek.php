<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'teknik_destek';
$template = new Template('Teknik Destek - Nokta Admin', $currentPage);
// head'i çağırıyoruz
$template->head();
$database = new Database();

$durumlar = $database->fetchAll("SELECT * FROM nokta_teknik_durum");
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
                <input type="hidden" id="sDurum" value="<?= !empty($_GET['sDurum']) ? $_GET['sDurum'] : 0 ?>">
                <div class="row g-6">
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-body">
                                <label for="tekniker">Tekniker Filtrele</label>
                                <select class="form-control" id="technician" name="technician">
                                    <option value=''>Tekniker Seçiniz</option>
                                    <option value='2'>Ali İstif</option>
                                    <option value='1'>Muammer Güngör</option>
                                    <option value='3'>İlker Karaca</option>
                                    <option value='4'>Ahmet Özdemir</option>
                                </select>
                                <div class="mt-1">
                                    <button id="filterBtn" class="btn btn-primary">Filtrele</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-body row">
                                <div class="col-6">
                                    <label for="start_date">Başlangıç Tarihi</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date">
                                </div>
                                <div class="col-6">
                                    <label for="end_date" >Bitiş Tarihi</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date">
                                </div>
                                <div class="mt-2 ml-3">
                                    <button id="filterBtnDate" class="btn btn-primary">Tarih Aralığı Filtrele</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-body row">
                                <div class="col-6">
                                    <label for="seri_no_ara">Seri No</label>
                                    <input type="text" class="form-control" id="seri_no_ara" name="seri_no_ara">
                                </div>
                                <div class="mt-2 ml-3">
                                    <button id="filterBtnSeri" class="btn btn-primary">Seri No Sorgula</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group d-flex flex-wrap" role="group">
                        <button class="btn btn-primary m-1 flex-fill" data-bs-toggle="modal" data-bs-target="#basvuruModal" data-basvur-id="1">Yeni Kayıt Ekle</button>
                        <a href="pages/genel/teknik_destek.php?sDurum=0" class="btn btn-secondary m-1 flex-fill">Tüm Kayıtlar</a>
                        <a href="pages/genel/teknik_destek.php?sDurum=1" class="btn btn-warning m-1 flex-fill">Beklenen Kayıtlar</a>
                        <a href="pages/genel/teknik_destek.php?sDurum=2" class="btn btn-danger m-1 flex-fill">Onaylanan Kayıtlar </a>
                        <a href="pages/genel/teknik_destek.php?sDurum=3" class="btn btn-info m-1 flex-fill">İşlemdeki Kayıtlar</a>
                        <a href="pages/genel/teknik_destek.php?sDurum=4" class="btn btn-success m-1 flex-fill">İşlemi Biten Kayıtlar</a>
                    </div>

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Arızalı Cihazlar</h5>
                        <div class="card">
                                <div class="table-responsive mt-1">
                                    <table id="deneme" class="table table-striped table-bordered second" >
                                        <thead class="bg-light">
                                            <tr class="border-0">
                                                <th class="border-0">Takip Kodu</th>
                                                <th class="border-0">Ürün Kodu</th>
                                                <th class="border-0">Müşteri</th>
                                                <th class="border-0">Telefon</th>
                                                <th class="border-0">Tarih</th>
                                                <th class="border-0">Tekniker</th>
                                                <th class="border-0">Ürün Durumu</th>
                                                <th class="border-0">Güncelle</th>
                                            </tr>
                                        </thead>
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
</div>
<!-- / Layout wrapper -->
    <div class="modal fade" data-backdrop="static" id="editKayitForm" tabindex="-1" role="dialog" aria-labelledby="editKayitFormLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title " id="editKayitFormLabel" name="baslik">Destek Kaydı
                        <i class="fa-solid fa-print fa-lg yazdir-btn ml-3"></i>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        <!-- Edit User Form -->
                        <form id="editDestekForm">
                            <div class="row">
                                <input style="display: none;" type="text" id="destek_id" name="destek_id">
                                <div class="col-sm-12">
                                    <label for="musteri" class="form-label">Müşteri Bilgileri</label>
                                    <input type="text" class="form-control" id="musteri" readonly>
                                    <input type="text" class="form-control" id="takip_kodu" hidden>
                                </div>
                                <div class="col-sm-6">
                                    <label for="tel" class="form-label">Telefon</label>
                                    <input type="tel" class="form-control" name="tel" id="tel">

                                </div>
                                <div class="col-sm-6">
                                    <label for="email" class="form-label">E-Posta</label>
                                    <input type="email" class="form-control" id="email"  readonly>
                                </div>
                                <div class="col-sm-12">
                                    <label for="adres" class="form-label">Adres </label>
                                    <textarea name="adres" id="adres"  class="form-control" readonly></textarea>
                                </div>
                                <div class="col-sm-6">
                                    <label for="fatura_no" class="form-label">Fatura Numarası </label>
                                    <input type="text" class="form-control" id="fatura_no" readonly>
                                </div>
                                <div class="col-sm-6">
                                    <label for="ad_soyad" class="form-label">Formu Dolduran Bilgileri </label>
                                    <input type="text" class="form-control" id="ad_soyad" readonly >
                                </div>
                                <div class="col-sm-12">
                                    <label for="aciklama" class="form-label">Açıklama </label>
                                    <textarea name="aciklama" id="aciklama"  class="form-control" readonly></textarea>
                                </div>
                                <div class="col-6">
                                    <label for="gonderim_sekli" class="form-label">Gönderim Şekli </label>
                                    <small style="color: red">1 - Kargo Gönderim 2- Elden Teslimat</small>
                                    <input type="text" class="form-control" id="gonderim_sekli" readonly >
                                </div>
                                <div class="col-6">
                                    <label for="kargo_firmasi" class="form-label">Kargo Firması </label>
                                    <input type="text" class="form-control" id="kargo_firmasi" readonly >
                                </div>
                                <div class="col-sm-3">
                                    <label for="urun_kodu" class="form-label">Urun Kodu</label>
                                    <input type="text" class="form-control urun_kodu" id="urun_kodu" value="" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label for="seri_no" class="form-label">Seri No</label>
                                    <input type="text" class="form-control seri_no" id="seri_no" value="">
                                </div>
                                <div class="col-sm-2">
                                    <label for="adet" class="form-label">Adet</label>
                                    <input type="text" class="form-control adet" id="adet" value="" readonly>
                                </div>
                                <div class="col-sm-4">
                                    <label for="urun_durum" class="form-label">Durum</label>
                                    <?php
                                    $durumss = $database->fetchAll("SELECT * FROM nokta_teknik_durum"); ?>
                                    <select class="form-control urun_durum" id="urun_durum" value="">
                                        <?php foreach ($durumss as $durum): ?>
                                            <option value="<?= $durum['id'] ?>"><?= $durum['durum'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label for="teslim_alan" class="form-label">Teslim Alan </label>
                                    <input type="text" class="form-control" id="teslim_alan" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label for="teslim_edilen" class="form-label">Teslim Edilen </label>
                                    <input type="text" class="form-control" id="teslim_edilen">
                                </div>
                                <div class="col-sm-3">
                                    <label for="teslim_tarihi" class="form-label">Teslim Tarihi</label>
                                    <input type="date" class="form-control" id="teslim_tarihi">
                                </div>
                            </div>

                            <div class="form-group mt-2">
                                <label for="tekniker">Tekniker**</label>
                                <select id='tekniker' name="tekniker" style="width: 100%;">
                                    <option value="">Tekniker Seçiniz</option>
                                    <?php
                                    $tekniker = $database->fetchAll("SELECT * FROM nokta_teknikerler");
                                    foreach($tekniker as $row) {
                                        ?>
                                        <option value='<?php echo $row['id']; ?>' ><?php echo $row['tekniker']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="yapilan_islemler">Yapılan İşlemler**</label>
                                <textarea name="yapilan_islemler" id="yapilan_islemler" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="foto_yukleme">Fotoğraf Yükleme</label>
                                <input type="file" id="foto_yukleme" class="form-control-file" accept="image/*" multiple>
                                <div id="yuklenen_fotograflar" class="mt-3"></div>
                            </div>
                            <div class="row m-2">
                                <div id="input-rows-container"></div>
                            </div>

                        </form>
                </div>
                <div class="modal-footer">
                    <div id="saveEditDestekContainer">
                        <button type="button" id="saveEditDestek" class="btn btn-primary">Güncelle</button>
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Basvuru Formu -->
    <div class="modal fade" data-bs-backdrop="static" id="basvuruModal" tabindex="-1" role="dialog" aria-labelledby="basvuruModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="basvuruModalLabel">Teknik Destek</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="applicationForm" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-sm-12">
                                <label for="musteri" class="form-label">Müşteri(Firma Bilgisi)</label>
                                <input type="text" class="form-control" id="musteri_id" hidden value="<?php if($_SESSION['id']){ echo $_SESSION['id'];} ?>">
                                <input type="text" class="form-control" id="musteri" value="<?php if($_SESSION['id']){ echo $_SESSION['firma'];} ?>" required>
                                <div class="invalid-feedback">Geçerli ad giriniz!</div>
                            </div>
                            <div class="col-sm-6">
                                <label for="tel" class="form-label">Telefon*</label>
                                <input type="tel" class="form-control" id="tel" placeholder="0(xxx)xxx xx xx" required>
                                <div class="invalid-feedback">Geçerli Telefon giriniz!</div>
                            </div>
                            <div class="col-sm-6">
                                <label for="email11" class="form-label">E-Posta*</label>
                                <input type="email" class="form-control" id="email11" placeholder="mail@example.com" required>
                                <div class="invalid-feedback">Geçerli e-posta giriniz!</div>
                            </div>
                            <div class="col-sm-12">
                                <label for="adres" class="form-label">Adres*</label>
                                <input type="text" class="form-control" id="adres" required>
                                <div class="invalid-feedback">Geçerli Adres giriniz!</div>
                            </div>
                            <div id="input-row-template" style="display: none;">
                                <div class="row mb-2">
                                    <div class="col-sm-4">
                                        <label for="urun_kodu" class="form-label">Ürün Kodu*</label>
                                        <input type="text" class="form-control urun_kodu" required>
                                        <div class="invalid-feedback">Geçerli Ürün Kodu giriniz!</div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="seri_no" class="form-label">Seri Numarası</label>
                                        <input type="text" class="form-control seri_no">
                                        <div class="invalid-feedback">Geçerli Seri Numarası giriniz!</div>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="adet" class="form-label">Adet*</label>
                                        <input type="text" class="form-control adet" required>
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn mt-4 remove-row-btn"><i class="fa-solid fa-circle-minus fa-lg"></i></button>
                                    </div>
                                </div>
                            </div>
                            <small style="color: red">Birden fazla ürün girmek için + işaretine tıklayınız !</small>
                            <div id="input-rows-container">
                                    <div class="row mb-2">
                                        <div class="col-sm-4">
                                            <label for="urun_kodu" class="form-label">Ürün Kodu*</label>
                                            <input type="text" class="form-control urun_kodu" required>
                                            <div class="invalid-feedback">Geçerli Ürün Kodu giriniz!</div>
                                        </div>
                                        <div class="col-sm-4">
                                            <label for="seri_no" class="form-label">Seri Numarası</label>
                                            <input type="text" class="form-control seri_no">
                                            <div class="invalid-feedback">Geçerli Seri Numarası giriniz!</div>
                                        </div>
                                        <div class="col-sm-2">
                                            <label for="adet" class="form-label">Adet*</label>
                                            <input type="text" class="form-control adet" required>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="btn mt-4 add-row-btn"><i class="fa-solid fa-circle-plus fa-lg"></i></button>
                                        </div>
                                    </div>
                                </div>

                            <div class="col-sm-6">
                                <label for="fatura_no" class="form-label">Fatura No</label>
                                <input type="text" class="form-control" id="fatura_no" >
                                <div class="invalid-feedback">Geçerli fatura numarası giriniz!</div>
                            </div>
                            <div class="col-sm-6">
                                <label for="ad_soyad" class="form-label">Formu Dolduran Bilgileri*</label>
                                <input type="text" class="form-control" id="ad_soyad" required placeholder="Ad / Soyad...">
                                <div class="invalid-feedback">Geçerli bilgi giriniz!</div>
                            </div>
                            <div class="col-sm-12">
                                <label for="aciklama" class="form-label">Açıklama*</label>
                                <textarea name="aciklama" id="aciklama"  class="form-control" required></textarea>
                                <div class="invalid-feedback">Geçerli Ürün Kodu giriniz!</div>
                            </div>
                            <div class="row mb-2 mt-2">
                                <div class="col-6">
                                    <label for="gonderim_sekli" class="form-label">Gönderim Şekli*</label>
                                    <select class="form-control" id="gonderim_sekli" name="gonderim_sekli" required>
                                        <option value="1">Kargo ile Gönderim</option>
                                        <option value="2">Elden Teslim</option>
                                    </select>
                                </div>
                                <div class="col-6" id="kargo_firmasi_div">
                                    <label for="kargo_firmasi11" class="form-label">Kargo Firması*</label>
                                    <select class="form-control" id="kargo_firmasi11" name="kargo_firmasi11" required>
                                        <option value="">Kargo Firması Seçiniz</option>
                                        <option value="Yurtiçi Kargo">Yurtiçi Kargo</option>
                                        <option value="MNG Kargo">MNG Kargo</option>
                                        <option value="Aras Kargo">Aras Kargo</option>
                                        <option value="Sürat Kargo">Sürat Kargo</option>
                                        <option value="PTT Kargo">PTT Kargo</option>
                                        <option value="Diğer Kargo Firmaları">Diğer Kargo Firmaları</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="checkbox">
                                    <input type="checkbox" id="onay" name="onay" required class="form-check-input"/>
                                    <a class="sozBtn" data-toggle="modal" data-target="#sozlesmeModal">
                                        Arıza Kayıt Sözleşmesini onaylıyorum.
                                    </a>
                                </div>
                            </div>
                        </div>
                        <hr class="my-4">
                        <button class="w-100 btn btn-primary btn-lg" style="background-color:#f29720; border-color:#f29720" type="submit">Gönder</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Sözleşme -->
    <div class="modal fade" data-bs-backdrop="static" id="sozlesmeModal" tabindex="-1" role="dialog" aria-labelledby="sozlesmeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sozlesmeModalLabel">Şartlar ve Koşullar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-sm-12">
                                    <p>1. Servis süresi en fazla 30 iş günüdür ve 90 gün takibi yapılmayan ürünler için şirketimiz hiçbir sorumluluk kabul etmez.
                                        <br>2. Garanti süreleri, Fatura düzenleme tarihinden itibaren başlar. Bunun dışında belirtilen Üretici Garantisi ancak üreticinin tespit ettiği koşullar çerçevesinde geçerlidir. Nokta Elektronik bu koşulları aynen müşteriye yansıtır.
                                        <br>3. Kurulum sırasında oluşan fiziksel ve elektriksel hatalar veya müşteriden kaynaklanan diğer donanım arızalarından dolayı servise gelmiş ürün garanti dışıdır ve servis ücreti alınır.
                                        <br>4. Teknik servis ücreti cari hesaba dahil olmayıp peşin olarak tahsil edilir.
                                        <br>5. Garanti harici tamir edilen ürünler teslimden itibaren 3 ay garantilidir.
                                        <br>6. Nokta Elektronik arızalı ürün servise geldiği anda, eğer kullanıcı hatasını tanımlayabiliyorsa, bunu belirtir ancak ürün daha sonraki test aşamaların da garanti dışı tutulabilir. İstenildiğinde Nokta Elektronik bu tür arızalar için Teknik Rapor verir.
                                        <br>7. Bu formu imzalayarak teslim eden şirket ve birey bu koşulları kabul eder. Bu ürünler firmamızın stoğundan çıktığı andan itibaren her türlü risk müşteriye aittir.</p>
                            </div>
                        </div>
                        <hr class="my-4">
                        <button class="w-100 btn btn-primary btn-lg sozOnay" style="background-color:#f29720; border-color:#f29720">sozlesmeyi_okudum_onayliyorum</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Success Modal -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="successModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="button" id="yazdirButton" class="btn btn-primary"><i class="fa-solid fa-print me-2"></i>Yazdır</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Core JS -->
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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
<script>
    document.querySelector('.yazdir-btn').addEventListener('click', function() {
        const musteri = document.getElementById('musteri').value;
        const tel = document.getElementById('tel').value;
        const email = document.getElementById('email').value;
        const adres = document.getElementById('adres').value;
        const fatura_no = document.getElementById('fatura_no').value;
        const urun_kodu = document.getElementById('urun_kodu').value;
        const seri_no = document.getElementById('seri_no').value;
        const adet = document.getElementById('adet').value;
        const takip_kodu = document.getElementById('takip_kodu').value;

        const printContent = `
        <html>
        <head>
            <title>Print</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                    padding: 20px;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                }
                h1 {
                    text-align: center;
                    color: #333;
                }
                .info {
                    margin-bottom: 15px;
                    padding: 10px;
                    border: 1px solid #eee;
                    border-radius: 4px;
                    background-color: #f9f9f9;
                }
                .info label {
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <h1>Destek Kaydı</h1>
            <div class="info"><label>Takip Kodu:</label> ${takip_kodu}</div>
            <div class="info"><label>Müşteri:</label> ${musteri}</div>
            <div class="info"><label>Telefon:</label> ${tel}</div>
            <div class="info"><label>E-Posta:</label> ${email}</div>
            <div class="info"><label>Adres:</label> ${adres}</div>
            <div class="info"><label>Fatura No:</label> ${fatura_no}</div>
            <div class="info"><label>Ürün Kodu:</label> ${urun_kodu}</div>
            <div class="info"><label>Seri No:</label> ${seri_no}</div>
            <div class="info"><label>Adet:</label> ${adet}</div>
        </body>
        </html>
    `;

        const newWindow = window.open('', '', 'width=600,height=400');
        newWindow.document.write(printContent);
        newWindow.document.close();
        newWindow.print();
    });
</script>
<script>
    $(document).ready(function() {
        $('#search-filter').on('keyup', function() {
            $('#tekniker').val(null).trigger('change'); // Reset the selected values
            var searchText = $(this).val().toLowerCase();
            $('#tekniker option').each(function() {
                var optionText = $(this).text().toLowerCase();
                if (optionText.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            $('#tekniker_urun').val(null).trigger('change'); // Reset the selected values
            var searchText = $(this).val().toLowerCase();
            $('#tekniker_urun option').each(function() {
                var optionText = $(this).text().toLowerCase();
                if (optionText.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        var teknikerMapping = {
            '1': 'Muammer Güngör',
            '2': 'Ali İstif',
            '3': 'İlker Karaca',
            '4': 'Ahmet Özdemir',
        };
        var dataTable = $('#deneme').DataTable({
            "language": {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json',
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "functions/teknik_destek/server_tdp.php",
                type: "GET",
                data: function(d) {
                    d.technician = $('#technician').val();
                    d.sDurum = $('#sDurum').val(); // Get sDurum value from select box
                    d.start_date = $('#start_date').val(); // Get start date value
                    d.end_date = $('#end_date').val(); // Get end date value
                    d.seri_no_ara = $('#seri_no_ara').val();
                }
            },
            "columns": [
                { data: 'takip_kodu' },
                {
                    data: 'urun_kodu',
                    render: function(data, type, row) {
                        // Eğer veri var ise büyük harfe çevir
                        return data ? data.toUpperCase() : '';
                    }
                },
                { data: 'musteri' },
                { data: 'tel' },
                { data: 'tarih' },
                {
                    data: 'tekniker',
                    render: function (data, type, row) {
                        if (!data) return data; // data boşsa olduğu gibi döndür

                        // Tek bir tekniker numarası veya virgülle ayrılmış tekniker numaraları olabilir
                        var teknikerList = data.split(',');
                        var teknikerNames = teknikerList.map(function(num) {
                            return teknikerMapping[num.trim()] || num; // Eşleşen tekniker ismini döndür veya orijinal numarayı döndür
                        });

                        // Teknisyen isimlerini virgülle ayrılmış string olarak döndür
                        return teknikerNames.join(', ');
                    }
                },
                { data: 'durum' },
                {
                    data: 'id',
                    render: function(data, type, full, meta) {
                        return '<button type="button" name="tdpDuzenle" value="Düzenle" class="btn btn-sm btn-outline-success edit-tdp-duzenle" data-tdp-id="'+data+'"><i class="far fa-edit"></i></button>' ;
                    },
                    orderable: false // Disable ordering on this column
                }
            ],
            "order": [[3, 'desc']] // Default order by 'tarih' column (index 3)
        });


        $('#filterBtn').on('click', function() {
            dataTable.draw();
        });
        $('#filterBtnDate').on('click', function() {
            dataTable.draw();
        });
        $('#filterBtnSeri').on('click', function() {
            dataTable.draw();
        });
        $('#editKayitForm').on('hidden.bs.modal', function() {
            // Reset the form fields
            $('#editDestekForm')[0].reset();
            // Clear dynamically added rows
            $('#input-rows-container').html('');
            // Clear uploaded photos preview
            $('#yuklenen_fotograflar').html('');
        });

        document.getElementById('foto_yukleme').addEventListener('change', function(event) {
            const files = event.target.files;
            const yuklenenFotograflar = document.getElementById('yuklenen_fotograflar');

            // Clear any previous images
            yuklenenFotograflar.innerHTML = '';

            if (files.length > 4) {
                alert('En fazla 4 fotoğraf yükleyebilirsiniz.');
                return;
            }

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100%';
                    img.style.maxWidth = '200px';  // Max width for the image preview
                    img.classList.add('img-thumbnail', 'mb-2');
                    yuklenenFotograflar.appendChild(img);
                };

                reader.readAsDataURL(file);
            }
        });
        $(document).on('click', '.edit-tdp-duzenle', function() {
            var tdpID = $(this).data('tdp-id');
            $('#editDestekForm')[0].reset();
            $('#input-rows-container').empty(); // Resetting container
            $.ajax({
                url: 'functions/teknik_destek/get_info.php',
                method: 'post',
                dataType: 'json',
                data: { id: tdpID, type: 'tdp' },
                success: function(response) {
                    console.log(response);
                    if (response.error) {
                        alert('Hata: ' + response.error);
                        return;
                    }
                    if (response.urun_durumu == '1' ) {
                        $('#saveEditDestekContainer').show();
                    }else if (response.urun_durumu == '2' ) {
                        $('#saveEditDestekContainer').show();
                    }else if (response.urun_durumu == '3' ) {
                        $('#saveEditDestekContainer').show();
                    }else{$('#saveEditDestekContainer').show();}
                    $('#musteri').val(response.musteri);
                    $('#tel').val(response.tel);
                    $('#email').val(response.mail);
                    $('#adres').val(response.adres);
                    $('#fatura_no').val(response.fatura_no);
                    $('#ad_soyad').val(response.teslim_eden);
                    $('#takip_kodu').val(response.takip_kodu);
                    $('#aciklama').val(response.aciklama);
                    $('#kargo_firmasi').val(response.kargo_firmasi);
                    $('#gonderim_sekli').val(response.gonderim_sekli);
                    $('#yapilan_islemler').val(response.yapilan_islemler);
                    $('#urun_kodu').val(response.urun_kodu);
                    $('#seri_no').val(response.seri_no);
                    $('#adet').val(response.adet);
                    $('#urun_durum').val(response.urun_durumu);
                    $('#teslim_tarihi').val(response.teslim_tarih);
                    $('#teslim_edilen').val(response.teslim_edilen);
                    $('#teslim_alan').val(response.teslim_alan);
                    $('#tekniker').val(response.tekniker);
                    $('#destek_id').val(response.id);
                    var foto_array = response.foto.split(',');
                    $('#yuklenen_fotograflar').empty();
                    foto_array.forEach(function(filename) {
                        if (filename.trim() !== '') {
                            var imgHtml = ` <img src="assets/images/teknik-destek/${filename}" class="img-thumbnail mr-2" style="width: 100px;"> `;
                            $('#yuklenen_fotograflar').append(imgHtml);
                        }
                    });
                    // Durum kontrolü
                    //alert(response.urun_durumu);

                },
                error: function(xhr, status, error) {
                    console.error('AJAX Hatası:', status, error);
                    alert('Sunucudan yanıt alınamadı!');
                }
            });
            $('#editKayitForm').modal('show');
        });

        $('#saveEditDestek').click(function() {
            var fotoVal = document.getElementById('foto_yukleme');
            var files = fotoVal.files;
            var destek_id = $('#destek_id').val();
            var tekniker = $('#tekniker').val();
            var seri_no = $('#seri_no').val();
            var teslim_edilen = $('#teslim_edilen').val();
            var teslim_tarih = $('#teslim_tarihi').val();
            var urun_durum = $('#urun_durum').val();
            var tel = $('#tel').val();
            var yapilan_islemler = $('#yapilan_islemler').val();
            var type = 'destek';

            var formData = new FormData();
            formData.append('id', destek_id);
            formData.append('tel', tel);
            formData.append('yapilan_islemler', yapilan_islemler);
            formData.append('tekniker', tekniker);
            formData.append('seri_no', seri_no);
            formData.append('urun_durum', urun_durum);
            formData.append('teslim_tarih', teslim_tarih);
            formData.append('teslim_edilen', teslim_edilen);
            formData.append('type', type);
            for (var i = 0; i < files.length; i++) {
                formData.append('foto[]', files[i]);
            }
            $.ajax({
                url: 'functions/teknik_destek/process_teknik.php',
                method: 'post',
                data: formData,
                processData: false,
                contentType: false,
                success: function(gel) {
                    $('#editKayitForm').modal('hide');
                    console.log(gel);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);

                    Swal.fire({
                        icon: 'success',
                        title: 'Güncelleme Kaydedilmiştir!',
                        toast: true,
                        position: 'top-end',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });
    });
</script>
<script>
     $(document).ready(function() {
        $('.sozBtn').click(function() {
            $('#sozlesmeModal').modal('show');
        });
        $('.sozOnay').click(function() {
            $("#onay").prop("checked", true);
            $('#sozlesmeModal').modal('hide');
        });
     });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var gonderimSekliSelect = document.getElementById("gonderim_sekli");
        var kargoFirmasiDiv = document.getElementById("kargo_firmasi_div");
        var kargoFirmasiInput = document.getElementById("kargo_firmasi11");

        // Initial check
        toggleKargoFirmasiVisibility();

        // Event listener for gonderim_sekli change
        gonderimSekliSelect.addEventListener("change", function() {
            toggleKargoFirmasiVisibility();
        });

        // Function to toggle kargo_firmasi visibility
        function toggleKargoFirmasiVisibility() {
            if (gonderimSekliSelect.value === "1") {
                kargoFirmasiDiv.style.display = "block";
                kargoFirmasiInput.setAttribute("required", "required");
            } else {
                kargoFirmasiDiv.style.display = "none";
                kargoFirmasiInput.removeAttribute("required");
            }
        }
    });
</script>
<script>
    $(document).ready(function() {
        $('.basvur-btn').click(function() {
            $('#basvuruModal').modal('show');
        });
        // Yeni giriş satırı ekleme işlevi
        function addInputRow() {
            var newRow = $('#input-row-template').clone().removeAttr('id').removeAttr('style');
            $('#input-rows-container').append(newRow);
        }
        // İlk satır ekleme olayı dinleyicisi
        $(document).on('click', '.add-row-btn', function() {
            addInputRow();
        });
        // Satır silme olayı dinleyicisi
        $(document).on('click', '.remove-row-btn', function() {
            $(this).closest('.row').remove();
        });
        $('#applicationForm').submit(function(e) {
            e.preventDefault();

            var urun_kodu_array = [];
            var seri_no_array = [];
            var adet_array = [];

            $('#input-rows-container .row').each(function() {
                var urun_kodu = $(this).find('.urun_kodu').val();
                var seri_no = $(this).find('.seri_no').val();
                var adet = $(this).find('.adet').val();

                urun_kodu_array.push(urun_kodu);
                seri_no_array.push(seri_no);
                adet_array.push(adet);
            });

            var formData = new FormData();
            formData.append('urun_kodu', urun_kodu_array.join(','));
            formData.append('seri_no', seri_no_array.join(','));
            formData.append('adet', adet_array.join(','));
            // Diğer form verilerini ekleyin
            formData.append('id', $('#musteri_id').val());
            formData.append('musteri', $('#musteri').val());
            formData.append('tel', $('#tel').val());
            formData.append('email', $('#email11').val());
            formData.append('adres', $('#adres').val());
            formData.append('fatura_no', $('#fatura_no').val());
            formData.append('aciklama', $('#aciklama').val());
            formData.append('ad_soyad', $('#ad_soyad').val());
            formData.append('onay', $('#onay').is(':checked') ? 1 : 0);
            formData.append('gonderim_sekli', $('#gonderim_sekli').val());
            formData.append('kargo_firmasi', $('#kargo_firmasi11').val());
            formData.append('type', 'ariza');

            $.ajax({
                type: 'POST',
                url: 'functions/edit_info.php',
                data: formData,
                processData: false,
                contentType: false,
                success: function(gelen) {
                    $('#basvuruModal').modal('hide');

                    // Modal içeriğini ayarlayın
                    $('#modalTitle').text("Başvurunuz Alınmıştır!");
                    $('#modalBody').html('Arıza Takip Kodunuz: ' + gelen);
                    $('#successModal').modal('show');

                    $('#yazdirButton').off('click').on('click', function() {
                        var urun_kodu = urun_kodu_array.join(', ');
                        var seri_no = seri_no_array.join(', ');
                        var adet = adet_array.join(', ');
                        var musteri = $('#musteri').val();
                        var tel = $('#tel').val();
                        var email = $('#email11').val();
                        var aciklama = $('#aciklama').val();

                        var printContent = `
                    <html>
                    <head>
                        <title>Yazdır</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                margin: 20px;
                            }
                            h3 {
                                color: #333;
                            }
                            p {
                                font-size: 14px;
                                line-height: 1.6;
                            }
                            strong {
                                color: #555;
                            }
                            .header {
                                text-align: center;
                                margin-bottom: 20px;
                            }

                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h3>Başvuru Bilgileri</h3>
                        </div>
                        <p><strong>Takip Kodu:</strong> ${gelen}</p>
                        <p><strong>Ürün Kodu:</strong> ${urun_kodu}</p>
                        <p><strong>Seri No:</strong> ${seri_no}</p>
                        <p><strong>Adet:</strong> ${adet}</p>
                        <p><strong>Müşteri:</strong> ${musteri}</p>
                        <p><strong>Telefon:</strong> ${tel}</p>
                        <p><strong>Email:</strong> ${email}</p>
                        <p><strong>Açıklama:</strong> ${aciklama}</p>

                    </body>
                    </html>
                `;

                        var newWindow = window.open('', '', 'width=600,height=400');
                        newWindow.document.write(printContent);
                        newWindow.document.close();
                        newWindow.onload = function() {
                            newWindow.print();
                            newWindow.onafterprint = function() {
                                newWindow.close();
                            };
                        };
                    });
                },
                error: function(response) {
                    if (response.status === 400) {
                        alert("Lütfen zorunlu alanları doldurunuz !");
                    }
                    if (response.status === 500) {
                        alert("Hatalı e-posta adresi !");
                    }
                    if (response.status === 600) {
                        alert("Lütfen Kargo Firmasını Doldurunuz !");
                    }
                }
            });
        });
        
    });
</script>