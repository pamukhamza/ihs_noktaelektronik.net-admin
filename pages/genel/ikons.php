<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';


$currentPage = 'ikons';
$template = new Template('İkonlar - NEBSİS', $currentPage);

// head'i çağırıyoruz
$template->head();
$database = new Database();
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
                            <div class="card-body">
                                <div class="mb-3">
                                    <button class="btn btn-primary add_catalog" data-bs-toggle="modal" data-bs-target="#editIkon">Add New</button>
                                </div>
                                <form>
                                    <div class="table-responsive text-nowrap">
                                        <table class="table table-hover">
                                            <thead>
                                            <tr>
                                                <th>İkon</th>
                                                <th>Title</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                                <?php
                                                    $database = new Database();
                                                    $query = "SELECT * FROM nokta_urunler_ikonlar";
                                                    $results = $database->fetchAll($query);
                                                foreach ($results as $row) {
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/ikons/<?= $row["img"]; ?>" style="max-width: 60px; max-height: 60px; object-fit: cover; border-radius: 5px;">
                                                        </td>
                                                        <td><?= $row["title"]; ?></td>
                                                        <td>
                                                            <a class="cursor-pointer me-2 edit-ikon" data-bs-toggle="modal" data-bs-target="#editIkon"
                                                                data-id="<?= $row["id"] ?>"
                                                                data-ikon_title="<?= $row['title']; ?>"
                                                                data-ikon_photo="<?= $row['img']; ?>"><i class="ti ti-pencil me-1"></i></a>
                                                            <a class="cursor-pointer delete_ikon" data-id="<?= $row['id']; ?>"><i class="ti ti-trash me-1"></i></a>
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
                <div class="content-backdrop fade"></div>
            </div>
            <?php $template->footer(); ?>
        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
</div>
<div class="modal fade" id="editIkon" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-cat">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2 catalog-title"></h4>
                </div>
                <form id="editIkonForm" class="row g-6" onsubmit="return false">
                    <div class="col-6">
                        <label class="form-label" for="ikon_title">Title</label>
                        <input type="text" id="ikon_title" name="ikon_title" class="form-control"/>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="ikon_photo">Photo</label>
                        <input type="file" class="form-control" id="ikon_photo" />
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
<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/libs/i18n/i18n.js"></script>
<script src="assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/form-layouts.js"></script>

<script>
    $(document).ready(function() {
        // Open modal for adding new catalog
        $(".add_catalog").on('click', function () {
            // Reset form fields
            $("#editIkonForm")[0].reset();
            // Remove any previous data attributes
            $("#editIkonForm").removeData("action").removeData("id");
            // Set action to insert
            $("#editIkonForm").data("action", "insert");
            $('.catalog-title').html("İkon Ekle");
            $('#editIkon').modal('show');
        });

        // Handle Edit catalog
        $(document).on('click', '.edit-ikon', function () {
            // Reset form fields
            $("#editIkonForm")[0].reset();
            // Remove previous data
            $("#editIkonForm").removeData("action").removeData("id");

            // Set new data
            const id = $(this).data('id');
            const ikon_title = $(this).data('ikon_title');
            const ikon_photo = $(this).data('ikon_photo');

            $("#ikon_title").val(ikon_title);
            $("#ikon_photo").val(''); // File input sıfırlanmalı
            $("#editIkonForm").data("action", "update").data("id", id);
            $('.catalog-title').html("İkon Düzenle");

            $('#editIkon').modal('show');
        });


        // Form Submission
        $("#editIkonForm").on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            let action = $(this).data("action");
            let id = $(this).data("id");

            let formData = new FormData();
            formData.append("ikon_photo", $("#ikon_photo")[0].files[0]);
            formData.append("ikon_title", $("#ikon_title").val());
            formData.append("action", action);
            formData.append("id", id);

            // AJAX Request
            $.ajax({
                url: 'functions/ikons/process_ikons.php', // PHP file to handle the request
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content type
                success: function (response) {
                    $('#editIkon').modal('hide');
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
                    $('#editIkon').modal('hide');
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
        $(".delete_ikon").on('click', function () {
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
                        data: { id: id, tablename: 'nokta_urunler_ikonlar', type: 'delete' },  // Type delete olarak gönderiliyor
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
    });
</script>
</body>
</html>