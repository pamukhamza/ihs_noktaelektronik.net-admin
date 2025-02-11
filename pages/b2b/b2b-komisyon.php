<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'b2b-banka-komisyonları';
$template = new Template('Banka Komisyonları - NEBSİS',  $currentPage);
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
                    <!-- Categories table -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <table id="deneme" class="table table-striped">
                                    <thead>
                                        <tr class="border-0">
                                            <th class="border-0">Grup Tanımı</th>
                                            <th class="border-0">Banka</th>
                                            <th class="border-0">Varsayılan</th>
                                            <th class="border-0">Aktif</th>
                                            <th class="border-0">Taksit Tanımları</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                               $query = "SELECT * FROM b2b_banka_kart_eslesme";
                                               $results = $database->fetchAll($query);
                                               foreach ($results as $kart) {
                                                $gosterim = $kart['varsayilan'];
                                                $yayin_durumu = $kart['aktif']; ?>
                                            <tr>
                                                <td class="text-center"><?php echo  $kart['grup_tanim']; ?></td>
                                                <td class="text-center"><?php echo  $kart['banka']; ?></td>
                                                <td></td>
                                                <td><?php if ($yayin_durumu == '1') {
                                                        echo '<i class="fas fa-check-circle text-success"></i> Aktif';
                                                    } elseif ($yayin_durumu == '0') {
                                                        echo '<i class="fas fa-times-circle text-danger"></i> Pasif';
                                                    } ?>
                                                </td>
                                                <td>
                                                    <button type="button" value="Desiler" class="btn btn-sm btn-outline-dark edit-banka" data-banka-id="<?= $kart['id']?>" ><i class="fa fa-list"></i></button>
                                                </td>
                                            </tr>
                                        <?php } ?>
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
</div>
<!-- Modal Popup Form -->
<div class="modal fade" data-backdrop="static"  id="bankaKomisyon" role="dialog" aria-labelledby="bankaKomisyonLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bankaKomisyonLabel" name="baslik">Taksit Seçenekleri</h5>
            </div>
            <div class="modal-body">
                <button type="button" class="btn btn-success mb-2 edit-taksit">Ekle</button>
                <!-- Edit Blog Form -->
                <div class="table table-responsive">
                    <table id="desi-table" class="table">
                        <thead class="bg-light">
                            <tr class="border-0">
                                <th class="border-0">Taksit</th>
                                <th class="border-0">Pos</th>
                                <th class="border-0">Vade</th>
                                <th class="border-0">Wolvow Eşleşme</th>
                                <th class="border-0">Aktif/Pasif</th>
                                <th class="border-0">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody id="sortable"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Taksit Seçenekleri Detay -->
<div class="modal fade" data-backdrop="static"  id="taksitSecenek" role="dialog" >
    <div class="modal-dialog modal-lg" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title1" id="taksitSecenekLabel" name="baslik"></h5>
            </div>
            <div class="modal-body">
                <!-- Edit Blog Form -->
                <form>
                    <div class="form-row">
                        <input type="text" id="taksitid" hidden name="taksitid">
                        <input type="text" id="kartid" hidden name="kartid">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="taksit">Taksit</label>
                                <input type="text" class="form-control" id="taksit">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="vade">Vade</label>
                                <input type="text" class="form-control" id="vade">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="aciklama">Açıklama</label>
                                <input type="text" class="form-control" id="aciklama">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="yonlendirme">Yönlendirme</label>
                            <select id="yonlendirme" class="form-control">
                                <option value="1">Param Pos</option>
                                <option value="2">Garanti Pos</option>
                                <option value="3">Kuveyt Pos</option>
                                <option value="4">Türkiye Finans</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="programeslestirme">Ticari Program Eşleştirme</label>
                            <select id="programeslestirme" class="form-control"></select>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary" id="saveTaksit">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
        $(document).on('click', '.edit-banka', function() {
            var bankaID = $(this).data('banka-id');

            $('#bankaKomisyon').modal('show');
            $('#kartid').val(bankaID);
            $.ajax({
                url: 'functions/b2b/muhasebe/get_banka.php',
                method: 'POST',
                data: { bankaID: bankaID},
                success: function(response) {
                    $('#sortable').html(response);
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Edit button click event handler
        $(document).on('click', '.edit-taksit', function() {
            var taksitId = $(this).data('taksit-id');
            var kartId = $(this).closest('tr').find('input[name="kart_id"]').val();
            if (taksitId) {
                $('.modal-title1').html("Taksit Düzenle");
            } else {
                $('.modal-title1').html("Taksit Ekle");
            }

            $.ajax({
                url: 'functions/b2b/muhasebe/get_banka_taksit.php',
                method: 'post',
                dataType: 'json',
                data: { id: taksitId,
                    type : 'taksit' },
                success: function(response) {
                    // Populate the modal with the fetched data
                    $('#taksit').val(response.taksit);
                    $('#vade').val(response.vade);
                    $('#aciklama').val(response.aciklama);
                    $('#yonlendirme').val(response.pos_id);
                    $('#programeslestirme').val(response.ticari_program);
                    $('#taksitid').val(response.id);
                },
                error: function(xhr, status, error) {
                    console.log(xhr);
                    console.error(error);
                }
            });
            // Show the edit modal
            $('#taksitSecenek').modal('show');
        });
        // Save button click event handler
        $('#saveTaksit').click(function() {
            var id = $('#taksitid').val();
            var taksit = $('#taksit').val();
            var kart_id = $('#kartid').val();
            var vade = $('#vade').val();
            var aciklama = $('#aciklama').val();
            var yonlendirme = $('#yonlendirme').val();
            var programeslestirme = $('#programeslestirme').val();
            var type = 'taksit';

            $.ajax({
                url: 'functions/b2b/muhasebe/edit_taksit.php',
                method: 'post',
                data: { id: id,
                        kart_id: kart_id,
                        taksit: taksit,
                        vade: vade,
                        aciklama: aciklama,
                        yonlendirme: yonlendirme,
                        programeslestirme : programeslestirme,
                        type: type},
                success: function() {
                    window.location.reload();
                    $('#taksitSecenek').modal('hide');
                }
            });
        });
        // Function to populate the select element
        function populateProgramOptions() {
            $.ajax({
                url: 'functions/b2b/muhasebe/get_program_options.php', // Update the URL to point to your PHP script
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Clear existing options
                    $('#programeslestirme').empty();

                    // Populate options
                    $.each(response, function(index, option) {
                        $('#programeslestirme').append($('<option>', {
                            value: option.id, // Use appropriate value from your table
                            text: option.id + ' - ' + option.BANKA_ADI + ' - ' + option.TANIMI + ' - Taksit Sayısı: ' + option.TAKSIT_SAYISI
                        }));
                    });
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error(error);
                }
            });
        }
        // Call the function to populate options on page load
        populateProgramOptions();

        $(document).on('click', '.aktifPasifBanka', function() {
            var id = $(this).attr("id");
            var konum = "b2b_banka_taksit_eslesme";
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
</body>
</html>
