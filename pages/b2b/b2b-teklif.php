<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'b2b-teklif';
$template = new Template('Teklifler - Nokta Admin',  $currentPage);
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
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-1">
                        <div class="card">
                            <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Teklifler</h5>
                            <div class="table-responsive">
                                <table id="deneme" class="table table-bordered second">
                                    <thead class="bg-light">
                                    <tr class="border-0">
                                        <th class="border-0">Ürün</th>
                                        <th class="border-0">Firma</th>
                                        <th class="border-0">Açıklama</th>
                                        <th class="border-0">Tarih</th>
                                        <th class="border-0">Detay</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $q = "SELECT bt.*, u.firmaUnvani, nu.UrunKodu FROM `b2b_teklif` AS bt 
                                                    LEFT JOIN uyeler AS u ON bt.uye_id = u.id
                                                    LEFT JOIN nokta_urunler AS nu ON nu.id = bt.urun_id";
                                            $urun = $database->fetchAll($q);
                                            foreach($urun as $row) {
                                            ?>
                                            <tr>
                                                <td><?php echo $row['UrunKodu']; ?></td>
                                                <td><?php echo $row['firmaUnvani']; ?></td>
                                                <td><?php echo $row['aciklama']; ?></td>
                                                <td><?php echo $row['kayit_tarihi']; ?></td>
                                                <td>
                                                    <button type="button" value="Düzenle" class="btn  btn-dark viewTeklif" data-form-id="<?php echo $row['id']; ?>"><i class="far fa-eye"></i></button>
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
<!-- / Layout wrapper -->
<!-- Modal Popup Form -->
<div class="modal fade" data-backdrop="static" id="iletisimModal" tabindex="-1" role="dialog" aria-labelledby="iletisimModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="iletisimModalLabel" name="baslik">Mesaj Detay</h5>
        
            </div>
            <div class="modal-body">
                <!-- İletişim Form -->
                <form id="iletisimForm" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="adSoyad" class="form-label">Firma</label>
                            <input type="text" class="form-control" id="adSoyad" readonly>
                        </div>
                        <div class="col-sm-6">
                            <label for="email" class="form-label">E-Posta Adresi</label>
                            <input type="email" class="form-control" id="email" readonly>
                        </div>
                        <div class="col-sm-12">
                            <label for="mesaj" class="form-label">Mesaj</label>
                            <textarea class="form-control" name="mesaj" id="mesaj" readonly></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary closeIletisim" data-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>
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
    $(document).ready(function() {
        $('.viewTeklif').click(function() {
            $('#iletisimModal').modal('show');
            var iletisimId = $(this).data('form-id');
            $.ajax({
                url: 'functions/b2b/teklif/get_info.php',
                method: 'post',
                dataType: 'json',
                data: { id: iletisimId, type: 'teklif' },
                success: function(response) {
                    $('#adSoyad').val(response.firmaUnvani);
                    $('#email').val(response.mail);
                    $('#mesaj').val(response.aciklama);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                    alert('AJAX Error: ' +  error);
                }
            });
        });
        $('.closeIletisim').click(function() {
            $('#iletisimModal').modal('hide');
        });
    });
</script>
</body>
</html>
