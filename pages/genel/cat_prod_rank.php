<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'categories';
$template = new Template('Kategori Ürün Sıralama - Nokta Admin', $currentPage);
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
                <div class="row g-6">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-content">
                                <!-- table head dark -->
                                <div class="table-responsive p-3">
                                    <table class="table table-striped table-bordered second" >
                                        <thead class="bg-light">
                                        <tr>
                                            <th class="border-0">Sıra</th>
                                            <th class="border-0">Ürün Kodu</th> 
                                            <th class="border-0">Ürün Adı</th>
                                            <th class="border-0">Ürün Düzenle</th>
                                        </tr>
                                        </thead>
                                        <?php
                                            // Retrieve the main category ID from the request
                                            $mainCategoryId = $_GET['id'];

                                            // Prepare and execute the query to get all categories including subcategories
                                            $q = "WITH RECURSIVE CategoryTree AS (
                                                    SELECT id FROM nokta_kategoriler WHERE id = :id
                                                    UNION ALL 
                                                    SELECT k.id FROM nokta_kategoriler k
                                                    INNER JOIN CategoryTree ct ON k.parent_id = ct.id )
                                                SELECT id FROM CategoryTree ";

                                            $categories = $database->fetchAll($q , array('id' => $mainCategoryId));

                                            // Sadece 'id' değerlerini almak için array_map kullanarak düz bir dizi oluştur
                                            $categoryIdsArray = array_map(function($category) {
                                                return $category['id'];
                                            }, $categories);

                                            // Düz diziyi virgülle ayırarak string haline getir
                                            $categoryIds = implode(',', $categoryIdsArray);

                                            // Eğer kategori bulunamazsa sorguyu çalıştırmamak için kontrol ekleyin
                                            if (!empty($categoryIds)) {
                                                // Kategoriye ait ürünleri çek
                                                $products = $database->fetchAll("SELECT id, sira, UrunKodu, UrunAdiTR FROM nokta_urunler WHERE KategoriID IN ($categoryIds) ORDER BY sira ASC ");
                                            } else {
                                                $products = []; // Eğer kategori yoksa, boş bir ürün listesi döndür
                                            }

                                        ?>
                                        <tbody id="sortable">
                                        <?php foreach($products as $row): ?>
                                            <tr data-id="<?= $row['id']; ?>">
                                                <td><?= $row['sira'] ?? '0'; ?></td>
                                                <td><?= $row['UrunKodu']; ?></td>
                                                <td><?= $row['UrunAdiTR']; ?></td>
                                                <td>
                                                    <a href="pages/genel/add-product.php?id=<?= $row['id']; ?>" target="_blank" class="cursor-pointer me-2 edit_product" data-product-id="<?= $row['id']; ?>"><i class="ti ti-pencil me-1"></i></a>
                                                    <a class="cursor-pointer delete_product" data-id="<?= $row['id']; ?>"><i class="ti ti-trash me-1"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Edit Category Modal -->
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
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Main JS -->
<script src="assets/js/main.js"></script>

<script>
    $(document).ready(function() {
        $("#sortable").sortable({
            update: function(event, ui) {
                // Get the updated order of items
                var newOrder = $(this).sortable('toArray', {attribute: 'data-id'}).map(Number);

                // Send updated order to server
                $.ajax({
                    url: 'functions/categories/update_rank.php', // Create this PHP file for handling the update
                    type: 'POST',
                    data: {newOrder: newOrder},
                    success: function(response) {
                        // Optionally handle response from server
                        location.reload();
                    }
                });
            }
        });
    });
    $(document).on('click', '.delete_product', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Emin misin?',
            text: "Geri Alınamaz!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Delete!',
            cancelButtonText: 'Cancel!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'functions/functions.php',
                    type: 'POST',
                    data: { id: id, type: 'deleteProduct' },  // Type delete olarak gönderiliyor
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Silindi!',
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
                            text: 'Failed.',
                        });
                    }
                });
            }
        });
    });
</script>
</body>
</html>
