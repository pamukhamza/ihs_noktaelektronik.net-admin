<?php
include_once '../../functions/db.php';
require '../functions/admin_template.php';
$currentPage = 'products';
$template = new Template('Ürünler - Lahora Admin', $currentPage);
// head'i çağırıyoruz
$template->head();
$db = new Database();
$deleteQuery = "DELETE FROM products WHERE (SKU IS NULL OR SKU = '') AND (name IS NULL OR name = '')";
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
                                        <th>ID</th>
                                        <th>SKU</th>
                                        <th>Ürün Adı</th>
                                        <th>Marka</th>
                                        <th>Kategorİ</th>
                                        <th>Özel Alan Ürün</th>
                                        <th>Öne Çıkan Ürün</th>
                                        <th>Aktİf</th>
                                        <th>İşlemler</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $database = new Database();
                                            $query = "
                                                SELECT p.*, c.name AS category_name
                                                FROM products p
                                                LEFT JOIN categories c ON p.category = c.id
                                            ";
                                            $results = $database->fetchAll($query);
                                            foreach ($results as $row) {
                                        ?>
                                            <tr>
                                                <td><?= $row['id']; ?></td>
                                                <td><?= $row['SKU']; ?></td>
                                                <td><?= $row['name']; ?></td>
                                                <td><?= $row['brand']; ?></td>
                                                <td><?= $row['category_name'] ?: 'No Category'; ?></td>
                                                <td>
                                                    <label class="switch switch-success">
                                                        <input type="checkbox" class="switch-input featured-checkbox" data-id="<?= $row['id']; ?>" <?= $row['featured'] == 1 ? 'checked' : ''; ?> />
                                                        <span class="switch-toggle-slider">
                                                            <span class="switch-on"><i class="ti ti-check"></i></span>
                                                            <span class="switch-off"><i class="ti ti-x"></i></span>
                                                        </span>
                                                        <span class="switch-label"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-success">
                                                        <input type="checkbox" class="switch-input new-checkbox" data-id="<?= $row['id']; ?>" <?= $row['new'] == 1 ? 'checked' : ''; ?> />
                                                        <span class="switch-toggle-slider">
                                                            <span class="switch-on"><i class="ti ti-check"></i></span>
                                                            <span class="switch-off"><i class="ti ti-x"></i></span>
                                                        </span>
                                                        <span class="switch-label"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-success">
                                                        <input type="checkbox" class="switch-input active-checkbox" data-id="<?= $row['id']; ?>" <?= $row['active'] == 1 ? 'checked' : ''; ?> />
                                                        <span class="switch-toggle-slider">
                                                            <span class="switch-on"><i class="ti ti-check"></i></span>
                                                            <span class="switch-off"><i class="ti ti-x"></i></span>
                                                        </span>
                                                        <span class="switch-label"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <a class="cursor-pointer me-2 edit_product" data-id="<?= $row['id']; ?>"><i class="ti ti-pencil me-1"></i></a>
                                                    <a class="cursor-pointer delete_product" data-id="<?= $row['id']; ?>"><i class="ti ti-trash me-1"></i></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
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
<!-- Main JS -->
<script src="../assets/js/main.js"></script>
<script>
    $(document).ready(function() {
        const table = $('#lang_table').DataTable();

        // Edit product tıklama olayı
        $('.edit_product').on('click', function() {
            const id = $(this).data('id'); // Tıklanan satırdaki ID'yi alıyoruz
            window.location.href = `add-product.php?id=${id}`; // add-product.php'ye yönlendiriyoruz
        });

        $(".delete_product").on('click', function () {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Silmek istediğinize emin misiniz?',
                text: "Bu işlem geri alınamaz!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Evet, sil!',
                cancelButtonText: 'Hayır, iptal et!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../functions/functions.php',
                        type: 'POST',
                        data: { id: id, tablename: 'products', type: 'delete' },  // Type delete olarak gönderiliyor
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Silindi!',
                                text: 'Kayıt başarıyla silindi.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();  // Sayfayı yeniden yükle
                            });
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata!',
                                text: 'Silme işlemi başarısız oldu.',
                            });
                        }
                    });
                }
            });
        });

        $('.new-checkbox').on('change', function() {
            var id = $(this).data('id');
            var newStatus = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '../functions/products/update_product_status.php',  // PHP dosyanızın ismini yazın
                type: 'POST',
                data: {
                    id: id,
                    field: 'new',
                    value: newStatus
                },
                success: function(response) {
                    console.log('New status updated:', response);
                },
                error: function() {
                    alert('Güncelleme sırasında bir hata oluştu.');
                }
            });
        });
        $('.featured-checkbox').on('change', function() {
            var id = $(this).data('id');
            var newStatus = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '../functions/products/update_product_status.php',  // PHP dosyanızın ismini yazın
                type: 'POST',
                data: {
                    id: id,
                    field: 'featured',
                    value: newStatus
                },
                success: function(response) {
                    console.log('New status updated:', response);
                },
                error: function() {
                    alert('Güncelleme sırasında bir hata oluştu.');
                }
            });
        });

        // ACTIVE field change
        $('.active-checkbox').on('change', function() {
            var id = $(this).data('id');
            var activeStatus = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '../functions/products/update_product_status.php',  // PHP dosyanızın ismini yazın
                type: 'POST',
                data: {
                    id: id,
                    field: 'active',
                    value: activeStatus
                },
                success: function(response) {
                    console.log('Active status updated:', response);
                },
                error: function() {
                    alert('Güncelleme sırasında bir hata oluştu.');
                }
            });
        });
    });
</script>
</body>
</html>