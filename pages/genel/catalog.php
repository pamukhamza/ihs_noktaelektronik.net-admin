<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$database = new Database();
$currentPage = 'catalog';
$template = new Template('Catalog - Nokta Admin', $currentPage);

// head'i çağırıyoruz
$template->head();
?>
<body>

<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar  ">
    <div class="layout-container">
        <?php $template->header(); ?>
        <!-- Content wrapper -->
        <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="row">
                    <div class="col">
                        <div class="card mb-6">
                            <div class="card-header px-0 pt-0">
                                <div class="nav-align-top">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#form-tabs-catalog" aria-controls="form-tabs-catalog" role="tab" aria-selected="true"><span class="ti ti-user ti-lg d-sm-none"></span><span class="d-none d-sm-block">Catalogs</span></button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <!-- catalog -->
                                    <div class="tab-pane fade active show" id="form-tabs-catalog" role="tabpanel">
                                        <div class="mb-3">
                                            <button class="btn btn-primary add_catalog" data-bs-toggle="modal" data-bs-target="#editCatalog">Add New</button>
                                        </div>
                                        <form>
                                            <div class="table-responsive text-nowrap">
                                                <table class="table table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Title</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="table-border-bottom-0">
                                                    <?php
                                                    $database = new Database();
                                                    $query = "SELECT * FROM catalogs";
                                                    $results = $database->fetchAll($query);
                                                    foreach ($results as $row) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $row["id"] ?></td>
                                                        <td><?= htmlspecialchars($row["title"]) ?></td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton<?= htmlspecialchars($row["id"]) ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Siteler
                                                                </button>
                                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?= htmlspecialchars($row["id"]) ?>">
                                                                    <li>
                                                                        <label class="switch switch-success">
                                                                            <input type="checkbox" class="switch-input wnet-checkbox" data-id="<?= htmlspecialchars($row["id"]) ?>" <?= $row['web_net'] == 1 ? 'checked' : '' ?> />
                                                                            <span class="switch-toggle-slider">
                                                                                <span class="switch-on"><i class="ti ti-check"></i></span>
                                                                                <span class="switch-off"><i class="ti ti-x"></i></span>
                                                                            </span>
                                                                            <span class="switch-label">.net</span>
                                                                        </label>
                                                                    </li>
                                                                    <li>
                                                                        <label class="switch switch-success">
                                                                            <input type="checkbox" class="switch-input wcomtr-checkbox" data-id="<?= htmlspecialchars($row["id"]) ?>" <?= $row['web_comtr'] == 1 ? 'checked' : '' ?> />
                                                                            <span class="switch-toggle-slider">
                                                                                <span class="switch-on"><i class="ti ti-check"></i></span>
                                                                                <span class="switch-off"><i class="ti ti-x"></i></span>
                                                                            </span>
                                                                            <span class="switch-label">.com.tr</span>
                                                                        </label>
                                                                    </li>
                                                                    <li>
                                                                        <label class="switch switch-success">
                                                                            <input type="checkbox" class="switch-input wcn-checkbox" data-id="<?= htmlspecialchars($row["id"]) ?>" <?= $row['web_cn'] == 1 ? 'checked' : '' ?> />
                                                                            <span class="switch-toggle-slider">
                                                                                <span class="switch-on"><i class="ti ti-check"></i></span>
                                                                                <span class="switch-off"><i class="ti ti-x"></i></span>
                                                                            </span>
                                                                            <span class="switch-label">.com.cn</span>
                                                                        </label>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <a class="cursor-pointer me-2 edit-catalog"
                                                               data-id="<?= $row["id"] ?>"
                                                               data-catalog_title="<?= $row['title']; ?>"
                                                               data-catalog_file="<?= $row['file']; ?>"><i class="ti ti-pencil me-1"></i></a>
                                                            <a class="cursor-pointer delete_catalog" data-id="<?= $row['id']; ?>"><i class="ti ti-trash me-1"></i></a>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Content -->

                <div class="content-backdrop fade"></div>

            </div>
            <?php $template->footer(); ?>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>
    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>

</div>
<!-- / Layout wrapper -->
<!-- Catalog Modal -->
<div class="modal fade" id="editCatalog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-cat">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2 catalog-title"></h4>
                </div>
                <form id="editCatalogForm" class="row g-6" onsubmit="return false">
                    <div class="col-6">
                        <label class="form-label" for="catalog_title">Title</label>
                        <input type="text" id="catalog_title" name="catalog_title" class="form-control"/>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="catalog_file">File</label>
                        <input type="file" class="form-control" id="catalog_file" />
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary me-3 submit_catalog">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Catalog Modal -->

<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/libs/i18n/i18n.js"></script>
<script src="assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Main JS -->
<script src="assets/js/main.js"></script>
<script src="assets/js/form-layouts.js"></script>

<script>
    $(document).ready(function() {
        // Open modal for adding new catalog
        $(".add_catalog").on('click', function () {
            $('.catalog-title').html("Katalog Ekle");
            $("#catalog_title").val('');
            $("#catalog_file").val('');
            $("#editCatalogForm").data("action", "insert"); // Set action to insert
        });

        // Handle Edit catalog
        $(document).on('click', '.edit-catalog', function () {
            $('.catalog-title').html("Katalog Düzenle");
            const id = $(this).data('id');
            const catalog_title = $(this).data('catalog_title');
            const catalog_file = $(this).data('catalog_file');

            $("#catalog_title").val(catalog_title);
            $("#catalog_file").val(catalog_file);
            $("#editCatalogForm").data("action", "update").data("id", id); // Set action to update and store ID
            $('#editCatalog').modal('show');
        });

        // Form Submission
        $("#editCatalogForm").on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            let action = $(this).data("action");
            let id = $(this).data("id");

            let formData = new FormData();
            formData.append("catalog_file", $("#catalog_file")[0].files[0]);
            formData.append("catalog_title", $("#catalog_title").val());
            formData.append("action", action);
            formData.append("id", id);

            // AJAX Request
            $.ajax({
                url: 'functions/catalogs/process_catalogs.php', // PHP file to handle the request
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content type
                success: function (response) {
                    $('#editCatalog').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response,
                        showConfirmButton: false,
                        timer: 1000
                    }).then(function() {
                        location.reload(1000);
                    });
                },
                error: function (xhr, status, error) {
                    $('#editCatalog').modal('hide');
                    console.error("AJAX Error: ", xhr, status, error); // Log error details
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: "An error occurred: " + error,
                        showConfirmButton: true
                    });
                }
            });
        });
        
        // Delete Catalogs
        $(".delete_catalog").on('click', function () {
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
                        url: 'functions/functions.php',
                        type: 'POST',
                        data: { id: id, tablename: 'catalogs', type: 'delete' },  // Type delete olarak gönderiliyor
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

        $(document).on('change', '.wnet-checkbox, .wcomtr-checkbox, .wcn-checkbox', function () {
            const id = $(this).data('id');
            const field = $(this).hasClass('wnet-checkbox') ? 'web_net' :
                          $(this).hasClass('wcomtr-checkbox') ? 'web_comtr' : 'web_cn';
            const value = $(this).is(':checked') ? 1 : 0;

            // AJAX isteği
            fetch('functions/catalogs/update_field.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: id,
                    field: field,
                    value: value
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {console.log('Güncelleme başarılı:', data.message);} 
                else {console.error('Güncelleme başarısız:', data.message);}
            })
            .catch(error => {
                console.error('Bir hata oluştu:', error);
            });
        });
    });

</script>
</body>
</html>