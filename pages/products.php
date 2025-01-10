<?php
include_once '../functions/db.php';
require '../functions/admin_template.php';
$currentPage = 'products';
$template = new Template('Ürünler - Nokta Admin', $currentPage);
// head'i çağırıyoruz
$template->head();
$db = new Database();
$deleteQuery = "DELETE FROM nokta_urunler WHERE (UrunKodu IS NULL OR UrunKodu = '') AND (UrunAdiTR IS NULL OR UrunAdiTR = '')";
$db->delete($deleteQuery);
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
                                        <th>Kategori</th>
                                        <th>ÖNE ÇIKANLAR</th>
                                        <th>WEB SİTELERİ</th>
                                        <th>İşlemler</th>
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
<script src="../assets/vendor/libs/jquery/jquery.js"></script>
<script src="../assets/vendor/js/bootstrap.js"></script>
<script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="../assets/vendor/libs/i18n/i18n.js"></script>
<script src="../assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- Main JS -->
<script src="../assets/js/main.js"></script>
<script>
    $(document).ready(function() {
        const table = $('#lang_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '../functions/products/get_products.php', // New PHP file to handle server-side processing
                type: 'POST'
            },
            columns: [
                { data: 'UrunKodu' },
                { data: 'UrunAdiTR' },
                { data: 'title' },
                { data: 'category_name', defaultContent: 'Kategori Yok' },
                { data: null, render: function(data, type, row) {
                    return `<div class="dropdown">
                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                    Özellikler
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${row.id}">
                                    <li>
                                        <label class="switch switch-success">
                                            <input type="checkbox" class="switch-input featured-checkbox" data-id="${row.id}" ${row.Vitrin == 1 ? 'checked' : ''} />
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"><i class="ti ti-check"></i></span>
                                                <span class="switch-off"><i class="ti ti-x"></i></span>
                                            </span>
                                            <span class="switch-label">Vitrin</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="switch switch-success">
                                            <input type="checkbox" class="switch-input new-checkbox" data-id="${row.id}" ${row.YeniUrun == 1 ? 'checked' : ''} />
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"><i class="ti ti-check"></i></span>
                                                <span class="switch-off"><i class="ti ti-x"></i></span>
                                            </span>
                                            <span class="switch-label">Yeni Ürün</span>
                                        </label>
                                    </li>
                                </ul>
                            </div>`;
                }},
                { data: null, render: function(data, type, row) {
                    return `<div class="dropdown">
                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                    Siteler
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${row.id}">
                                    <li>
                                        <label class="switch switch-success">
                                            <input type="checkbox" class="switch-input featured-checkbox" data-id="${row.id}" ${row.web_net == 1 ? 'checked' : ''} />
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"><i class="ti ti-check"></i></span>
                                                <span class="switch-off"><i class="ti ti-x"></i></span>
                                            </span>
                                            <span class="switch-label">.net</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="switch switch-success">
                                            <input type="checkbox" class="switch-input new-checkbox" data-id="${row.id}" ${row.web_comtr == 1 ? 'checked' : ''} />
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"><i class="ti ti-check"></i></span>
                                                <span class="switch-off"><i class="ti ti-x"></i></span>
                                            </span>
                                            <span class="switch-label">.com.tr</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="switch switch-success">
                                            <input type="checkbox" class="switch-input new-checkbox" data-id="${row.id}" ${row.web_cn == 1 ? 'checked' : ''} />
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"><i class="ti ti-check"></i></span>
                                                <span class="switch-off"><i class="ti ti-x"></i></span>
                                            </span>
                                            <span class="switch-label">.com.cn</span>
                                        </label>
                                    </li>
                                </ul>
                            </div>`;
                }},
                { data: null, render: function(data, type, row) {
                    return `<a class="cursor-pointer me-2 edit_product" data-product-id="${row.id}"><i class="ti ti-pencil me-1"></i></a>
                            <a class="cursor-pointer delete_product" data-id="${row.id}"><i class="ti ti-trash me-1"></i></a>`;
                }}
            ],
            dom: 'Bfrtip',
        buttons: [
            {
                text: 'Excel\'e Aktar',
                className: 'btn btn-success',
                action: function (e, dt, button, config) {
                    $.ajax({
                        url: '../functions/products/get_products.php',
                        type: 'POST',
                        data: { allData: true }, // Sunucuya tüm verileri talep ettiğinizi belirtin
                        success: function (response) {
                            // Gelen tüm verilerle DataTable oluştur
                            const allData = JSON.parse(response);

                            // DataTable'daki verileri DataTables'ın Excel butonuna aktarmak
                            const workbook = XLSX.utils.book_new();
                            const worksheet = XLSX.utils.json_to_sheet(allData);
                            XLSX.utils.book_append_sheet(workbook, worksheet, 'Ürünler');
                            XLSX.writeFile(workbook, 'urunler.xlsx');
                        }
                    });
                }
            }
        ]
        });
    });
</script>
<script>
        $(document).on('click', '.edit_product', function() {
        var id = $(this).data('product-id');
        console.log('Product ID:', id); // Debugging
        if (id) {
            window.location.href = 'add-product.php?id=' + id;
        } else {
            console.error('Product ID is undefined.');
        }
    });
    </script>
</body>
</html>