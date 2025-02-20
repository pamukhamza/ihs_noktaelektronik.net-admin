<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$database = new Database();

$currentPage = 'filters';
$template = new Template('Filtreler - NEBSİS Admin', $currentPage);

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
                                    <button class="btn btn-primary add_filter_title" data-bs-toggle="modal" data-bs-target="#editFilter">Yeni Filtre Başlığı Ekle</button>
                                </div>
                                <table id="filter_table" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Filter Başlığı</th>
                                            <th>Eşleşen Kategori</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody id="filter-title-list"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Edit filter Modal -->
                <div class="modal fade" id="editFilter" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-simple modal-edit-filter-title">
                        <div class="modal-content">
                            <div class="modal-body">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                <div class="text-center mb-6">
                                    <h4 class="mb-2 filter_t_title"></h4>
                                </div>
                                <form id="editFilterForm" class="row g-6" onsubmit="return false">
                                    <div class="col-6">
                                        <label class="form-label" for="modalEditName">Filtre Başlığı</label>
                                        <input type="text" id="modalEditName" name="modalEditName" class="form-control" placeholder="Filtre Başlığı" />
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="modalEditNameEn">Filtre Başlığı En</label>
                                        <input type="text" id="modalEditNameEn" name="modalEditNameEn" class="form-control" placeholder="Filtre Başlığı En" />
                                    </div>
                                    <?php
                                    $query = "SELECT id, KategoriAdiTr FROM nokta_kategoriler WHERE parent_id = 0";
                                    $categories = $database->fetchAll($query);
                                    ?>
                                    <div class="col-12">
                                        <label class="form-label" for="modalEditCategory">Eşleşen Kategori</label>
                                        <select id="modalEditCategory" name="modalEditCategory" class="form-control">
                                            <option value="">Kategori Seçiniz</option> <!-- Default empty option -->
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>">
                                                    <?php echo $category['KategoriAdiTr']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Main JS -->
<script src="assets/js/main.js"></script>

<script>
    $(document).ready(function() {
        // Load top-level categories
        loadCategories(0);

        // Load categories based on parent ID
        function loadCategories() {
            $.ajax({
                url: 'functions/filter/get_filter_title.php', // Endpoint to fetch categories
                type: 'GET',
                success: function(data) {
                    $('#filter-title-list').append(data);
                },
                error: function(xhr, status, error) {
                    console.error("An error occurred while loading brands:", error);
                }
            });
        }
        

        // Open modal for adding new category
        $(".add_filter_title").on('click', function () {
            $('.filter_t_title').html("Yeni Filtre Başlığı Ekle");
            $("#modalEditName").val('');
            $("#modalEditNameEn").val('');
            $("#editFilterForm").data("action", "insert");
        });
        // Open modal for editing category
        $(document).on('click', '.edit_filter_title', function () {
            $(".filter_t_title").html('Filtre Başlığı Düzenle');
            const name = $(this).data('name');
            const nameen = $(this).data('nameen');
            const id = $(this).data('id');

            $("#modalEditName").val(name);
            $("#modalEditNameEn").val(nameen);
            $("#editFilterForm").data("action", "update").data("id", id);

            // Kategori verisini almak için AJAX isteği gönderiyoruz
            $.ajax({
                url: 'functions/filter/get_category_for_filter.php',
                type: 'GET',
                data: { filter_title_id: id },
                success: function (response) {
                    console.log(response); // Log the raw response
                    const data = JSON.parse(response); // Should be valid JSON
                    $("#modalEditCategory").val(data.category_id);
                    $('#editFilter').modal('show');
                },
                error: function (xhr, status, error) {
                    console.error("An error occurred while loading category for filter:", error);
                }
            });

        });


        // Handle form submission
        $("#editFilterForm").on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData();
            let action = $(this).data("action");
            let id = action === "update" ? $(this).data("id") : null;

            formData.append("name", $("#modalEditName").val());
            formData.append("nameEn", $("#modalEditNameEn").val());
            formData.append("action", action);
            formData.append("category_id", $("#modalEditCategory").val()); // Add category_id to the FormData
            formData.append("id", id);

            $.ajax({
                url: 'functions/filter/process_filter_title.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#editFilter').modal('hide');
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
                    $('#editFilter').modal('hide');
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

        $(document).on('click', '.list_filter_title', function() {
            var id = $(this).data('id');
            window.location.href = 'pages/genel/filter_values.php?id=' + id;
        });


        $(document).on('click', '.delete_filter_title', function () {
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
                        data: { id: id, type: 'deleteFilterTitle' },  // Type delete olarak gönderiliyor
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
</body>
</html>
