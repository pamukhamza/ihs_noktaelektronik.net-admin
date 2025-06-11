<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'b2b-odemeler';
$template = new Template('TAHSİLAT - NEBSİS',  $currentPage);
$template->head();
$database = new Database();
?>
<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php $template->header(); ?>
        <div class="content-wrapper">
            <div class=" flex-grow-1 container-p-y container-xxl">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
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
                                                <th class="border-0">Tür</th>
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
            <div class="content-backdrop fade"></div>
        </div>
        <?php $template->footer(); ?>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
</div>
<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="assets/js/main.js"></script>
<script>
    $(document).ready(function () {
        var table = $('#odemeler').DataTable({
            processing: true,
            serverSide: true,
            order: [[5, 'desc']], // tarih sütunu
            ajax: {
                url: 'functions/b2b/muhasebe/server_odemeler.php',
                type: 'GET',
                data: function (d) {
                    d.minTutar = $('#minTutar').val();
                    d.maxTutar = $('#maxTutar').val();
                    d.minTarih = $('#minTarih').val();
                    d.maxTarih = $('#maxTarih').val();
                    d.basarili = $('#basarili').val();
                }
            },
            columns: [
                { data: 0, title: 'Sanal Pos' },
                { data: 1, title: 'Firma Adı' },
                { data: 2, title: 'Cari Kodu' },
                { data: 3, title: 'İşlem' },
                { data: 4, title: 'Tür' },
                { data: 5, title: 'Tarih' },
                { data: 6, title: 'Tutar' },
                { data: 7, title: 'Durum' },
                { data: 8, title: 'Dekont' }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Turkish.json'
            }
        });

    });
</script>
</body>
</html>