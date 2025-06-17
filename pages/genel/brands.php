<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$database = new Database();

$currentPage = 'brands';
$template = new Template('Markalar - NEBSİS Admin', $currentPage);

// head'i çağırıyoruz
$template->head();
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
                    <!-- Categories table -->
                    <div class="col-xxl-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <div class="mb-3">
                                    <button class="btn btn-primary add_lang" data-bs-toggle="modal" data-bs-target="#editCat">Yeni Marka Ekle</button>
                                </div>
                                <table id="cat_table" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th></th>
                                            <th>Marka Adı</th>
                                            <th>Sıralama</th>
                                            <th>Siteler</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody id="brand-list"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Edit Category Modal -->
                <div class="modal fade" id="editCat" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-simple modal-edit-cat">
                        <div class="modal-content">
                            <div class="modal-body">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                <div class="text-center mb-6">
                                    <h4 class="mb-2 cat-title"></h4>
                                </div>
                                <form id="editCatForm" class="row g-6" onsubmit="return false">
                                    <div class="col-6">
                                        <label class="form-label" for="modalEditName">Marka Adı</label>
                                        <input type="text" id="modalEditName" name="modalEditName" class="form-control" placeholder="Marka" />
                                    </div>
                                    <div class="col-6" id="cat_image_div">
                                        <label class="form-label" for="cat_image">Fotoğraf</label>
                                        <input type="file" class="form-control" accept="image/*" id="cat_image" />
                                    </div>
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-primary me-3">Gönder</button>
                                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">İptal</button>
                                    </div>
                                </form>
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
<script src="assets/js/main.js"></script>

<script>
    $(document).ready(function() {
        // Load top-level categories
        loadCategories(0);
        // Load categories based on parent ID
        function loadCategories() {
            $.ajax({
                url: 'functions/brands/get_brands.php', // Endpoint to fetch categories
                type: 'GET',
                success: function(data) {
                    $('#brand-list').append(data);
                },
                error: function(xhr, status, error) {
                    console.error("An error occurred while loading brands:", error);
                }
            });
        }
        $(document).on('click', '.brand_sort', function() {
            window.location.href = 'pages/genel/brand-sorting';
        });
        // Open modal for adding new category
        $(".add_lang").on('click', function () {
            $('.cat-title').html("Yeni Marka Ekle");
            $("#modalEditName").val('');
            $("#editCatForm").data("action", "insert");
        });
        // Open modal for editing category
        $(document).on('click', '.edit_brand', function () {
            $(".cat-title").html('Marka Düzenle');
            const name = $(this).data('name');
            const id = $(this).data('id');

            $("#modalEditName").val(name);
            $("#editCatForm").data("action", "update").data("id", id);
            $('#editCat').modal('show');
        });
        // Handle form submission
        $("#editCatForm").on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData();
            let action = $(this).data("action");
            let id = action === "update" ? $(this).data("id") : null;

            formData.append("name", $("#modalEditName").val());
            formData.append("cat_img", $("#cat_image")[0].files[0]); // Get the first file selected
            formData.append("action", action);
            formData.append("id", id);

            $.ajax({
                url: 'functions/brands/process_brands.php',
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content type
                success: function (response) {
                    $('#editCat').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: response,
                        showConfirmButton: false,
                        timer: 1000
                    }).then(function() {
                        location.reload(); // Reload the page to reflect changes
                    });
                },
                error: function (xhr, status, error) {
                    $('#editCat').modal('hide');
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: "Bir hata oluştu: " + error,
                        showConfirmButton: true
                    });
                    console.log(error);
                }
            });
        });
        $(document).on('click', '.delete_brand', function () {
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
                        data: { id: id, type: 'deleteBrand' },  // Type delete olarak gönderiliyor
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
        
    });
</script>
<script>
    $(document).ready(function () {
        $(document).on('change', '.wnet-checkbox, .wcomtr-checkbox, .wcn-checkbox', function () {
            const id = $(this).data('id');
            const field = $(this).hasClass('wnet-checkbox') ? 'web_net' :
                        $(this).hasClass('wcomtr-checkbox') ? 'web_comtr' : 'web_cn';
            const value = $(this).is(':checked') ? 1 : 0;

            // AJAX isteği
            fetch('functions/brands/update_field.php', {
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
                if (data.success) {
                    console.log('Güncelleme başarılı:', data.message);
                } else {
                    console.error('Güncelleme başarısız:', data.message);
                }
            })
            .catch(error => {
                console.error('Bir hata oluştu:', error);
            });
        });
    });
</script>
</body>
</html>
