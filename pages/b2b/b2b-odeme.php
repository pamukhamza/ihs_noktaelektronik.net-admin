<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'b2b-odemeler';
$template = new Template('Döviz Ayarları - NEBSİS',  $currentPage);
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
            <div class=" flex-grow-1 container-p-y container-xxl">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card">
                            <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Ödeme Filtreleme</h5>
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Min Tutar:</label>
                                        <input type="text" id="minTutar" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Max Tutar:</label>
                                        <input type="text" id="maxTutar" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Min Tarih:</label>
                                        <input type="text" id="minTarih" class="form-control datepicker">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Max Tarih:</label>
                                        <input type="text" id="maxTarih" class="form-control datepicker">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Durum:</label>
                                        <select id="basarili" class="form-control">
                                            <option value="">Tümü</option>
                                            <option value="1">Başarılı</option>
                                            <option value="0">Başarısız</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mt-4">
                                        <button id="filterBtn" class="btn btn-primary">Ödemeler</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-5">
                            <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Ödemeler</h5>
                            <div class="card-body">
                                <div class="table-responsive" id="employee_table">
                                    <table id="odemeler" class="table table-striped table-bordered second" style="width:100%">
                                        <thead class="bg-light">
                                            <tr class="border-0">
                                                <th class="border-0">Sanal Pos</th>
                                                <th class="border-0">Firma Adı</th>
                                                <th class="border-0">Cari Kodu</th>
                                                <th class="border-0">İşlem</th>
                                                <th class="border-0">Tarih</th>
                                                <th class="border-0">Tutar</th>
                                                <th class="border-0">Durum</th>
                                                <th class="border-0">Dekont</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
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
<!-- / Layout wrapper -->
<!-- Core JS -->
<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- jQuery UI -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!-- Main JS -->
<script src="assets/js/main.js"></script>
<script>
$(document).ready(function() {
    var table = $('#odemeler').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "functions/b2b/muhasebe/server_odemeler.php",
            "type": "POST",
            "data": function(d) {
                d.minTarih = $('#minTarih').val();
                d.maxTarih = $('#maxTarih').val();
                d.basarili = $('#basarili').val();
                d.minTutar = $('#minTutar').val();
                d.maxTutar = $('#maxTutar').val();
            }
        },
        "pageLength": 10,
        "order": [[4, 'desc']],
        "columns": [
            { "data": "pos_id", "render": function(data) {
                if (data == 1) return "Param Pos";
                else if (data == 2) return "Garanti Pos";
                else if (data == 3) return "Kuveyt Pos";
                else if (data == 4) return "Türkiye Finans Pos";
                else return "Diğer Poslar";
            }},
            { "data": "firmaUnvani" },
            { "data": "muhasebe_kodu" },
            { "data": "islem" },
            { "data": "tarih" },
            { "data": "tutar" },
            { "data": "basarili", "render": function(data) {
                return data == 1 ? "İşlem Başarılı" : "İşlem Başarısız";
            }},
            { "data": "dekont", "render": function(data, type, row) {
                return row.islem_turu === 'cari' ? `<a target="_blank" href="assets/uploads/dekontlar/${data}"><i class="far fa-file"></i></a>` : '';
            }}
        ],
        "colReorder": true,
        "initComplete": function() {
            $('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' });

            $('#filterBtn').on('click', function() {
                table.ajax.reload();
            });
        }
    });
});
</script>
</body>
</html>
