<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';
$currentPage = 'b2b-havuzbayileri';
$template = new Template('Havuz Bayileri - NEBSİS',  $currentPage);
// head'i çağırıyoruz
$template->head();
$database = new Database();
$satis_id = 86732; 
?>
<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php $template->header(); ?>
        <!-- Content wrapper -->
        <div class="content-wrapper">
            <!-- Content -->
            <div class=" flex-grow-1 container-p-y container-p-x">
                <div class="row g-6">
                    <!-- Categories table -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <table id="deneme" class="table table-striped">
                                    <thead>
                                        <tr class="border-0">
                                            <th class="border-0">Tipi</th>
                                            <th class="border-0">Ad Soyad</th>
                                            <th class="border-0">Firma Adı</th>
                                            <th class="border-0">Email</th>
                                            <th class="border-0">Muhasebe Kodu</th>
                                            <th class="border-0">Kimlik No</th>
                                            <th class="border-0">Telefon</th>
                                            <th class="border-0">Şehir/İlçe</th>
                                            <th class="border-0">Fiyat No</th>
                                            <th class="border-0">Kayıt Tarihi</th>
                                            <th class="border-0">Son Giriş</th>
                                            <th class="border-0">Satış Temsilcisi</th>
                                            <th class="border-0">Durum</th>
                                            <th class="border-0">İşlemler</th>
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
<!-- Core JS -->
<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Main JS -->
<script src="assets/js/main.js"></script>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<script>
     $(document).ready(function() {
          $(document).on('click', '.edit-uyeDuzenle', function() {
               var id = $(this).data('uye-id');
               window.location.href = 'pages/b2b/b2b-uye-duzenle.php?w=noktab2b&id=' + id; // Artı işareti (+) kullanılmalı
          });
     });
</script>
<script>
    $(document).ready(function() {
        var satisId = '<?php echo $satis_id; ?>';
        var table = $('#deneme').DataTable({
            "language": {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json',
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "functions/uyeler/datatable_uyeler_satis.php",
                "type": "POST",
                "data": function(d) {
                    d.satis_id = satisId;
                },
                "error": function(xhr, error, thrown) {
                    console.error("AJAX Hatası:", xhr.responseText);
                }
            },
            "columns": [
                { "data": "id" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        // Combine 'ad' and 'soyad' and return as a single string
                        return row.ad + ' ' + row.soyad;
                    }
                },
                { "data": "firmaUnvani" },
                { "data": "email" },
                { "data": "muhasebe_kodu" },
                { "data": "tc_no" },
                { "data": "tel" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return row.il_adi + ' / ' + row.ilce_adi;
                    }
                },
                { "data": "fiyat" },
                { "data": "kayit_tarihi" },
                { "data": "son_giris" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return row.full_name ;
                    }
                },

                {
                    "data": null, "orderable": false,
                    "render": function(data, type, row){
                        return`
                        <label class="switch switch-success">
                            <input type="checkbox" class="switch-input aktifPasifUye" name="${data.id}" id="${data.id}"  data-id="${data.id}"  ${data.aktif == 1 ? 'checked' : ''} />
                            <span class="switch-toggle-slider">
                                <span class="switch-on"><i class="ti ti-check"></i></span>
                                <span class="switch-off"><i class="ti ti-x"></i></span>
                            </span>
                        </label>
                        `;
                    }
                },
                {
                    "data": null, "orderable": false,
                    "render": function (data, type, row) {
                        return `<button type="button" name="uyeDuzenle" value="Düzenle" class="btn btn-sm btn-success edit-uyeDuzenle" data-uye-id="${data.id}" data-toggle="tooltip" title="Düzenle"><i class="fa-regular fa-pen-to-square"></i></button> `;
                    }
                }
            ],
            "order": [
                [9, "desc"] // kayit_tarihi sütununa göre artan sıralama (9 sütun indeksi)
            ],
            "colReorder": true,
            "initComplete": function() {
            }
        });
    });
</script>
</body>
</html>