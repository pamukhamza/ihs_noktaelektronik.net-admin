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
                    <?php 
                        $beklenen   = $database->fetchColumn("SELECT COUNT(*) FROM teknik_destek_urunler WHERE urun_durumu = 1 AND SILINDI = 0");
                        $serviste   = $database->fetchColumn("SELECT COUNT(*) FROM teknik_destek_urunler WHERE urun_durumu = 2 AND SILINDI = 0");
                        $islemde    = $database->fetchColumn("SELECT COUNT(*) FROM teknik_destek_urunler WHERE urun_durumu = 3 AND SILINDI = 0");
                        $islembitti = $database->fetchColumn("SELECT COUNT(*) FROM teknik_destek_urunler WHERE urun_durumu NOT IN (1, 2, 3) AND SILINDI = 0");
                    ?>
                    <div class="d-flex flex-wrap">
                        <button class="btn btn-primary m-1 flex-fill" data-bs-toggle="modal" data-bs-target="#basvuruModal" data-basvur-id="1">
                            Yeni Kayıt Ekle
                        </button>

                        <a href="pages/genel/teknik_destek.php?sDurum=0" class="btn btn-secondary m-1 flex-fill">
                            Tüm Kayıtlar
                        </a>

                        <a href="pages/genel/teknik_destek.php?sDurum=1" class="btn btn-warning m-1 flex-fill d-flex justify-content-between align-items-center">
                            <span>Bekleyen Kayıtlar</span>
                            <span class="badge bg-dark"><?= $beklenen ?></span>
                        </a>

                        <a href="pages/genel/teknik_destek.php?sDurum=2" class="btn btn-danger m-1 flex-fill d-flex justify-content-between align-items-center">
                            <span>Onaylanan Kayıtlar</span>
                            <span class="badge bg-light text-dark"><?= $serviste ?></span>
                        </a>

                        <a href="pages/genel/teknik_destek.php?sDurum=3" class="btn btn-info m-1 flex-fill d-flex justify-content-between align-items-center">
                            <span>İşlemdeki Kayıtlar</span>
                            <span class="badge bg-light text-dark"><?= $islemde ?></span>
                        </a>

                        <a href="pages/genel/teknik_destek.php?sDurum=4" class="btn btn-success m-1 flex-fill d-flex justify-content-between align-items-center">
                            <span>İşlemi Biten Kayıtlar</span>
                            <span class="badge bg-light text-dark"><?= $islembitti ?></span>
                        </a>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
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
    });
</script>
