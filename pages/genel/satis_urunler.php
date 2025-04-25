<?php 
include_once '../../functions/db.php';
require '../../functions/admin_template.php';
$currentPage = 's_urunler';
$template = new Template('Ürünler - NEBSİS',  $currentPage);
// head'i çağırıyoruz
$template->head();
$database = new Database();
?>
<body>
<div class="layout-wrapper layout-content-navbar  ">
    <div class="layout-container">
        <?php $template->header(); ?>
        <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="row g-6">
                    <div class="col-xxl-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <table id="lang_table" class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Ürün Kodu</th>
                                        <th>Ürün Adı</th>
                                        <th>Marka</th>
                                        <th>KATEGORİ</th>
                                        <th>DSF4</th>
                                        <th>DSF3</th>
                                        <th>DSF2</th>
                                        <th>DSF1</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Server-side processing will handle data fetching -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-backdrop fade"></div>
            </div>
            <?php $template->footer(); ?>
        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
</div>
<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/libs/i18n/i18n.js"></script>
<script src="assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- Main JS -->
<script src="assets/js/main.js"></script>
<script>
    $(document).ready(function () {
        const table = $('#lang_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'functions/products/get_products.php',
                type: 'POST'
            },
            columns: [
                { data: 'UrunKodu' },
                { data: 'UrunAdiTR' },
                { data: 'title' },
                { data: 'category_name', defaultContent: 'Kategori Yok' },
                {   data: 'DSF4',
                    render: function (data, type, row) {
                        return parseFloat(data).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + row.DOVIZ_BIRIMI;
                    }
                },{ data: 'DSF3',
                    render: function (data, type, row) {
                        return parseFloat(data).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + row.DOVIZ_BIRIMI;
                    }
                },{ data: 'DSF2',
                    render: function (data, type, row) {
                        return parseFloat(data).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + row.DOVIZ_BIRIMI;
                    }
                },{ data: 'DSF1',
                    render: function (data, type, row) {
                        return parseFloat(data).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + row.DOVIZ_BIRIMI;
                    }
                }
            ]
        });
    });
</script>
</body>
</html>