<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$satis_id = $_SESSION["id"]; 
$currentPage = 'b2b-uyeler';
$template = new Template('Üyeler - NEBSİS',  $currentPage);
// head'i çağırıyoruz
$template->head();
$database = new Database();
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
                                asasdas
                                <?php echo $satis_id; ?>
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
        var table = $('#deneme').DataTable({
            "language": {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json',
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "functions/uyeler/datatable_uyeler.php",
                "type": "POST",
                "data": function(d) {
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
                        return row.username ;
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
                        return `
                                <button type="button" class="btn btn-sm btn-danger" onclick="dynamicSil('${data.id}', '', 'uye', 'Üye Silindi!', 'pages/b2b/b2b-uyeler.php');" data-toggle="tooltip" title="Sil"><i class="fa-solid fa-trash-can"></i></button>
                                <button type="button" name="uyeDuzenle" value="Düzenle" class="btn btn-sm btn-success edit-uyeDuzenle" data-uye-id="${data.id}" data-toggle="tooltip" title="Düzenle"><i class="fa-regular fa-pen-to-square"></i></button>
                                <a href="mailto:${data.email}" name="uyeMail" value="Mail" class="btn btn-sm btn-info edit-uyeMail" data-uyeMail-id="${data.id}" data-toggle="tooltip" title="Mail Gönder"><i class="fa-solid fa-envelope"></i></a>
                                <button type="button" name="uyegirisyap" value="Giriş Yap" class="btn btn-sm btn-dark edit-uyegirisyap" data-uyegirisyap-id="${data.id}" data-toggle="tooltip" title="Üye Adına Giriş Yap"><i class="fa-solid fa-arrow-right-to-bracket"></i></button>
                            
                        `;
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
<script>
    $(document).ready(function() {
        $(document).on('click', '.aktifPasifUye', function() {
            var id = $(this).attr("id");
            var konum = "uyeler";
            var durum = ($(this).is(':checked')) ? '1' : '0';
            $.ajax({
                type: 'POST',
                url: 'functions/aktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
                data: { id:id, durum: durum, konum: konum },
                success: function (result) {
                    Swal.fire({
                        title: "Aktif/Pasif işlemi yapıldı!" ,
                        toast: true,
                        position: 'bottom-end',
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function () {
                    alert('Hata');
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        $(document).on('click', '.edit-uyegirisyap', function() {
            var userId = $(this).data('uyegirisyap-id');
            $.ajax({
                url: 'functions/uyeler/ses_uye.php',
                method: 'post',
                data: { userId: userId },
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                dataType: 'json', // Beklenen yanıt türü
                success: function(response) {
                    if (response.status === 'success') {
                        window.open('https://www.noktaelektronik.com.tr', '_blank');
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('AJAX hatası: ' + status + ' ' + error);
                }
            });
        });
    });
</script>
<script>
    function dynamicSil(gid, gel, customType, successMessage, redirectPage) {
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
                    url: 'functions/uyeler/delete_uye.php',
                    type: 'POST',
                    data: {
                        'gid': gid,
                        'gel': gel,
                        type: customType
                    },
                    success: function () {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: successMessage /* Ürün Silindi! */,
                            showConfirmButton: false,
                            timer: 1000
                        });
                        setTimeout(function () {
                            window.location.href = redirectPage /* adminSlider.php */;
                        }, 1000);
                    }
                });
            }
        });
    }
</script>
</body>
</html>