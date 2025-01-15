<?php
include_once '../functions/db.php';
require '../functions/admin_template.php';

$currentPage = 'categories';
$template = new Template('Kategoriler - Nokta Admin', $currentPage);
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
                    <!-- Categories table -->
                    <div class="col-xxl-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <div class="mb-3">
                                    <button class="btn btn-primary add_lang" data-bs-toggle="modal" data-bs-target="#editCat">Yeni Kategori Ekle</button>
                                </div>
                                <table id="cat_table" class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Kategorİ Adı</th>
                                        <th>Kategorİ Adı En</th>
                                        <th>İşlemler</th>
                                    </tr>
                                    </thead>
                                    <tbody id="category-list"></tbody>
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
                                        <label class="form-label" for="modalEditName">Kategori Adı</label>
                                        <input type="text" id="modalEditName" name="modalEditName" class="form-control" placeholder="Kategori" />
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="modalEditNameCn">Kategori Adı En</label>
                                        <input type="text" id="modalEditNameCn" name="modalEditNameCn" class="form-control" placeholder="category" />
                                    </div>
                                    <?php
                                    // Fetch all categories
                                    $query = "SELECT * FROM nokta_kategoriler";
                                    $results = $database->fetchAll($query);

                                    // Create an associative array to store categories by their ID
                                    $categories = [];
                                    foreach ($results as $row) {
                                        $categories[$row['id']] = $row;
                                    }

                                    // Function to generate options for the select dropdown
                                    function generateCategoryOptions($categories, $parentId = 0, $level = 0) {
                                        $options = '';
                                        foreach ($categories as $category) {
                                            if ($category['parent_id'] == $parentId) { // Assuming there's a 'parent_id' field
                                                // Indent based on the level
                                                $indent = str_repeat('&nbsp;', $level * 4);
                                                $options .= "<option value=\"{$category['id']}\">{$indent}-{$category['KategoriAdiTr']}</option>";
                                                // Recursive call for subcategories
                                                $options .= generateCategoryOptions($categories, $category['id'], $level + 1);
                                            }
                                        }
                                        return $options;
                                    }
                                    ?>

                                    <div class="col-6">
                                        <label class="form-label" for="modalEditCategory">Üst Kategori</label>
                                        <select id="modalEditCategory" name="modalEditCategory" class="select2 form-select">
                                            <?php echo generateCategoryOptions($categories); ?>
                                        </select>
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
<script src="../assets/vendor/libs/jquery/jquery.js"></script>
<script src="../assets/vendor/js/bootstrap.js"></script>
<script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="../assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Main JS -->
<script src="../assets/js/main.js"></script>

<script>
    $(document).ready(function() {
        // Load top-level categories
        loadCategories(0);

        // Load categories based on parent ID
        function loadCategories(parentId) {
            $.ajax({
                url: '../functions/categories/get_categories.php', // Endpoint to fetch categories
                type: 'GET',
                data: { parent_id: parentId },
                success: function(data) {
                    $('#category-list').append(data);
                },
                error: function(xhr, status, error) {
                    console.error("An error occurred while loading categories:", error);
                }
            });
        }

        // Toggle subcategories
        $(document).on('click', '.toggle-subcat', function() {
            const categoryId = $(this).data('id');
            const subcatRows = $('.subcat-' + categoryId);
            subcatRows.toggle(); // Toggle display
            $(this).text($(this).text() === '+' ? '-' : '+');
        });

        $(document).on('click', '.cat_sort', function() {
            const id = $(this).data('id');
            window.location.href = 'category-sorting?id=' + id;
        });

        // Open modal for adding new category
        $(".add_lang").on('click', function () {
            $('.cat-title').html("Yeni Kategori Ekle");
            $("#modalEditName").val('');
            $("#modalEditNameCn").val('');
            $("#modalEditCategory").val(null).trigger("change");
            $("#editCatForm").data("action", "insert");
        });

        // Open modal for editing category
        $(document).on('click', '.edit_cat', function () {
            $(".cat-title").html('Kategori Düzenle');
            const name = $(this).data('name');
            const name_cn = $(this).data('name_cn');
            const category = $(this).data('category');
            const id = $(this).data('id');

            $("#modalEditName").val(name);
            $("#modalEditNameCn").val(name_cn);
            $("#modalEditCategory").val(category).trigger("change");
            $("#editCatForm").data("action", "update").data("id", id);
            $('#editCat').modal('show');

            if (category === 0) {
                $("#cat_image_div").show(); // Assuming you have a wrapping div with this ID
            } else {
                $("#cat_image_div").hide(); // Hide the div if category is not 0
            }
        });

        // Handle form submission
        $("#editCatForm").on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData();
            let action = $(this).data("action");
            let id = action === "update" ? $(this).data("id") : null;

            formData.append("name", $("#modalEditName").val());
            formData.append("name_cn", $("#modalEditNameCn").val());
            formData.append("category", $("#modalEditCategory").val());
            formData.append("cat_img", $("#cat_image")[0].files[0]); // Get the first file selected
            formData.append("action", action);
            formData.append("id", id);

            $.ajax({
                url: '../functions/categories/process_category.php',
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
                }
            });
        });

        $(document).on('click', '.delete_cat', function () {
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
                        url: '../functions/functions.php',
                        type: 'POST',
                        data: { id: id, type: 'deleteCat' },  // Type delete olarak gönderiliyor
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
