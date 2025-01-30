<?php
include_once '../functions/db.php';
require '../functions/admin_template.php';

$database = new Database();

$currentPage = 'slider';
$template = new Template('Slider - Nokta Admin', $currentPage);

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
                                            <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#form-tabs-net" aria-controls="form-tabs-net" role="tab" aria-selected="true"><span class="d-sm-none">NET</span><span class="d-none d-sm-block">NET</span></button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#form-tabs-b2b" aria-controls="form-tabs-b2b" role="tab" aria-selected="false"><span class="d-sm-none">B2B</span><span class="d-none d-sm-block">B2B</span></button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#form-tabs-b2c" aria-controls="form-tabs-b2c" role="tab" aria-selected="false"><span class="d-sm-none">B2C</span><span class="d-none d-sm-block">B2C</span></button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <!-- NET -->
                                    <div class="tab-pane fade active show" id="form-tabs-net" role="tabpanel">
                                        <div class="mb-3">
                                            <button class="btn btn-primary add_slider" data-bs-toggle="modal" data-bs-target="#editSlider">Yeni Ekle</button>
                                        </div>
                                        <form>
                                            <div class="table-responsive text-nowrap">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Link</th>
                                                            <th>Photo</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="table-border-bottom-0">
                                                    <?php
                                                    $database = new Database();
                                                    $query = "SELECT * FROM slider WHERE site = 'net'";
                                                    $results = $database->fetchAll($query);
                                                    foreach ($results as $row) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $row["id"] ?></td>
                                                        <td></td>
                                                        <td>
                                                            <ul class="list-unstyled m-0 avatar-group d-flex align-items-center">
                                                                <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" class="avatar avatar-xs pull-up">
                                                                    <img src="../assets/images/index/<?= $row["slider_photo"] ?>" alt="photo" class="rounded-circle">
                                                                </li>
                                                            </ul>
                                                        </td>
                                                        <td>
                                                            <label class="switch switch-success">
                                                                <input type="checkbox" class="switch-input active-checkbox-slider" data-id="<?= $row['id']; ?>" <?= $row['status'] == 1 ? 'checked' : ''; ?> />
                                                                <span class="switch-toggle-slider">
                                                                    <span class="switch-on"><i class="ti ti-check"></i></span>
                                                                    <span class="switch-off"><i class="ti ti-x"></i></span>
                                                                </span>
                                                                <span class="switch-label"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <a class="cursor-pointer me-2 edit-slider"
                                                               data-id="<?= $row["id"] ?>"
                                                               data-slider_link="<?= $row['slider_link']; ?>"
                                                               data-slider_photo="<?= $row['slider_photo']; ?>"><i class="ti ti-pencil me-1"></i></a>
                                                            <a class="cursor-pointer delete_slider" data-id="<?= $row['id']; ?>"><i class="ti ti-trash me-1"></i></a>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- B2B -->
                                    <div class="tab-pane fade" id="form-tabs-b2b" role="tabpanel">
                                        <div class="mb-3">
                                            <button class="btn btn-primary add_slider" data-bs-toggle="modal" data-bs-target="#editSlider">Yeni Ekle</button>
                                        </div>
                                        <form>
                                            <div class="table-responsive text-nowrap">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Link</th>
                                                            <th>Photo</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="table-border-bottom-0">
                                                    <?php
                                                    $database = new Database();
                                                    $query = "SELECT * FROM slider WHERE site = 'b2b'";
                                                    $results = $database->fetchAll($query);
                                                    foreach ($results as $row) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $row["id"] ?></td>
                                                        <td></td>
                                                        <td>
                                                            <ul class="list-unstyled m-0 avatar-group d-flex align-items-center">
                                                                <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" class="avatar avatar-xs pull-up">
                                                                    <img src="../assets/images/index/<?= $row["slider_photo"] ?>" alt="photo" class="rounded-circle">
                                                                </li>
                                                            </ul>
                                                        </td>
                                                        <td>
                                                            <label class="switch switch-success">
                                                                <input type="checkbox" class="switch-input active-checkbox-slider" data-id="<?= $row['id']; ?>" <?= $row['status'] == 1 ? 'checked' : ''; ?> />
                                                                <span class="switch-toggle-slider">
                                                                    <span class="switch-on"><i class="ti ti-check"></i></span>
                                                                    <span class="switch-off"><i class="ti ti-x"></i></span>
                                                                </span>
                                                                <span class="switch-label"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <a class="cursor-pointer me-2 edit-slider"
                                                               data-id="<?= $row["id"] ?>"
                                                               data-slider_link="<?= $row['slider_link']; ?>"
                                                               data-slider_photo="<?= $row['slider_photo']; ?>"><i class="ti ti-pencil me-1"></i></a>
                                                            <a class="cursor-pointer delete_slider" data-id="<?= $row['id']; ?>"><i class="ti ti-trash me-1"></i></a>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- B2C -->
                                    <div class="tab-pane fade" id="form-tabs-b2c" role="tabpanel">
                                        <div class="mb-3">
                                            <button class="btn btn-primary add_slider" data-bs-toggle="modal" data-bs-target="#editSlider">Yeni Ekle</button>
                                        </div>
                                        <form>
                                            <div class="table-responsive text-nowrap">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Link</th>
                                                            <th>Photo</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="table-border-bottom-0">
                                                    <?php
                                                    $database = new Database();
                                                    $query = "SELECT * FROM slider WHERE site = 'b2c'";
                                                    $results = $database->fetchAll($query);
                                                    foreach ($results as $row) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $row["id"] ?></td>
                                                        <td></td>
                                                        <td>
                                                            <ul class="list-unstyled m-0 avatar-group d-flex align-items-center">
                                                                <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" class="avatar avatar-xs pull-up">
                                                                    <img src="../assets/images/index/<?= $row["slider_photo"] ?>" alt="photo" class="rounded-circle">
                                                                </li>
                                                            </ul>
                                                        </td>
                                                        <td>
                                                            <label class="switch switch-success">
                                                                <input type="checkbox" class="switch-input active-checkbox-slider" data-id="<?= $row['id']; ?>" <?= $row['status'] == 1 ? 'checked' : ''; ?> />
                                                                <span class="switch-toggle-slider">
                                                                    <span class="switch-on"><i class="ti ti-check"></i></span>
                                                                    <span class="switch-off"><i class="ti ti-x"></i></span>
                                                                </span>
                                                                <span class="switch-label"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <a class="cursor-pointer me-2 edit-slider"
                                                               data-id="<?= $row["id"] ?>"
                                                               data-slider_link="<?= $row['slider_link']; ?>"
                                                               data-slider_photo="<?= $row['slider_photo']; ?>"><i class="ti ti-pencil me-1"></i></a>
                                                            <a class="cursor-pointer delete_slider" data-id="<?= $row['id']; ?>"><i class="ti ti-trash me-1"></i></a>
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
<!-- Edit Slider Modal -->
<div class="modal fade" id="editSlider" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-cat">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2 slider-title"></h4>
                </div>
                <form id="editSliderForm" class="row g-6" onsubmit="return false">
                    <div class="col-6">
                        <label class="form-label" for="slider_link">Link</label>
                        <input type="text" id="slider_link" name="slider_link" class="form-control" placeholder="category" />
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="slider_img">Image</label>
                        <input type="file" class="form-control" accept="image/*" id="slider_img" />
                    </div>
                    <div class="col-6">
                        <select class="form-select" id="slider_site" name="slider_site">
                            <option value="net">NET</option>
                            <option value="b2b">B2B</option>
                            <option value="b2c">B2C</option>
                        </select>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary me-3 submit_slider">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Edit Slider Modal -->

<script src="../assets/vendor/libs/jquery/jquery.js"></script>
<script src="../assets/vendor/js/bootstrap.js"></script>
<script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="../assets/vendor/libs/i18n/i18n.js"></script>
<script src="../assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Main JS -->
<script src="../assets/js/main.js"></script>
<script src="../assets/js/form-layouts.js"></script>

<script>
    $(document).ready(function() {
        // Open modal for adding new slider
        $(".add_slider").on('click', function () {
            $('.slider-title').html("Yeni Slider Ekle");
            $("#slider_link").val('');
            $("#slider_photo").val(''); // Clear slider photo input
            $("#editSliderForm").data("action", "insert"); // Set action to insert
            $("#slider_img").val(''); // Clear any previous image input
        });

        // Handle Edit Slider
        $(document).on('click', '.edit-slider', function () {
            $('.slider-title').html("Slider Düzenle");
            const id = $(this).data('id');
            const slider_link = $(this).data('slider_link');
            const slider_photo = $(this).data('slider_photo');
            const slider_site = $(this).data('slider_site');

            $("#slider_link").val(slider_link);
            $("#slider_photo").val(slider_photo);
            $("#slider_site").val(slider_site);
            $("#editSliderForm").data("action", "update").data("id", id); // Set action to update and store ID
            $('#editSlider').modal('show');
        });

        // Form Submission
        $("#editSliderForm").on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            let action = $(this).data("action");
            let id = $(this).data("id");

            let formData = new FormData();
            formData.append("slider_link", $("#slider_link").val());
            formData.append("slider_img", $("#slider_img")[0].files[0]);
            formData.append("slider_site", $("#slider_site").val());
            formData.append("action", action);
            formData.append("id", id);

            // AJAX Request
            $.ajax({
                url: '../functions/slider/process_slider.php', // PHP file to handle the request
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content type
                success: function (response) {
                    $('#editSlider').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response,
                        showConfirmButton: false,
                        timer: 1000
                    }).then(function() {
                        location.reload(); // Reload the page to reflect changes
                    });
                },
                error: function (xhr, status, error) {
                    $('#editSlider').modal('hide');
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
    });

</script>
<script>
    $(".delete_slider").on('click', function () {
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
                    data: { id: id, tablename: 'slider', type: 'delete' },  // Type delete olarak gönderiliyor
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
    $('.active-checkbox-slider').on('change', function() {
        var id = $(this).data('id');
        var activeStatus = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: '../functions/update_status.php',  // PHP dosyanızın ismini yazın
            type: 'POST',
            data: {
                id: id,
                field: 'status',
                value: activeStatus,
                database: 'slider'
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    text: response,
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function() {
                alert('Error while updating');
            }
        });
    });
    $('.active-checkbox-banner').on('change', function() {
        var id = $(this).data('id');
        var activeStatus = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: '../functions/update_status.php',  // PHP dosyanızın ismini yazın
            type: 'POST',
            data: {
                id: id,
                field: 'status',
                value: activeStatus,
                database: 'banner'
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    text: response,
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function() {
                alert('Error while updating');
            }
        });
    });
</script>

</body>
</html>