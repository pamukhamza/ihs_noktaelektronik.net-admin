<?php
include_once '../../functions/db.php';
require '../functions/admin_template.php';

$database = new Database();

$currentPage = 'slider';
$template = new Template('Slider, Banner, Poster - Lahora Admin', $currentPage);

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
                                            <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#form-tabs-slider" aria-controls="form-tabs-slider" role="tab" aria-selected="true"><span class="ti ti-user ti-lg d-sm-none"></span><span class="d-none d-sm-block">Slider</span></button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#form-tabs-banner" aria-controls="form-tabs-banner" role="tab" aria-selected="false"><span class="ti ti-phone ti-lg d-sm-none"></span><span class="d-none d-sm-block">Banner</span></button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#form-tabs-poster" aria-controls="form-tabs-poster" role="tab" aria-selected="false"><span class="ti ti-edit ti-lg d-sm-none"></span><span class="d-none d-sm-block">Poster</span></button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <!-- Slider -->
                                    <div class="tab-pane fade active show" id="form-tabs-slider" role="tabpanel">
                                        <div class="mb-3">
                                            <button class="btn btn-primary add_slider" data-bs-toggle="modal" data-bs-target="#editSlider">Add New</button>
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
                                                    $query = "SELECT * FROM slider";
                                                    $results = $database->fetchAll($query);
                                                    foreach ($results as $row) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $row["id"] ?></td>
                                                        <td><?= $row["slider_link"] ?></td>
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
                                    <!-- Banner -->
                                    <div class="tab-pane fade" id="form-tabs-banner" role="tabpanel">
                                        <form>
                                            <p style="color: red">w:600px<br> h:350px</p>
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
                                                    $query = "SELECT * FROM banner";
                                                    $results = $database->fetchAll($query);
                                                    foreach ($results as $row) {
                                                        ?>
                                                        <tr>
                                                            <td><?= $row["id"] ?></td>
                                                            <td><?= $row["banner_link"] ?></td>
                                                            <td>
                                                                <ul class="list-unstyled m-0 avatar-group d-flex align-items-center">
                                                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" class="avatar avatar-xs pull-up">
                                                                        <img src="../assets/images/index/<?= $row["banner_photo"] ?>" alt="photo" class="rounded-circle">
                                                                    </li>
                                                                </ul>
                                                            </td>
                                                            <td>
                                                                <label class="switch switch-success">
                                                                    <input type="checkbox" class="switch-input active-checkbox-banner" data-id="<?= $row['id']; ?>" <?= $row['status'] == 1 ? 'checked' : ''; ?> />
                                                                    <span class="switch-toggle-slider">
                                                                    <span class="switch-on"><i class="ti ti-check"></i></span>
                                                                    <span class="switch-off"><i class="ti ti-x"></i></span>
                                                                </span>
                                                                    <span class="switch-label"></span>
                                                                </label>
                                                            </td>
                                                            <td>
                                                                <a class="cursor-pointer me-2 edit-banner"
                                                                   data-id="<?= $row["id"] ?>"
                                                                   data-banner_link="<?= $row["banner_link"] ?>"><i class="ti ti-pencil me-1"></i></a>
                                                                <a class="cursor-pointer" href="javascript:void(0);"><i class="ti ti-trash me-1"></i></a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- Poster -->
                                    <div class="tab-pane fade" id="form-tabs-poster" role="tabpanel">
                                        <?php
                                        $query = "SELECT * FROM poster";
                                        $p_result = $database->fetch($query);
                                        ?>
                                        <form>
                                            <div class="row g-6">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="p-text">Text</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="p-text" class="form-control" value="<?= $p_result["text"] ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="p-sm-text">Small Text</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="p-sm-text" class="form-control" value="<?= $p_result["small_text"] ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="bb-text">Black Box Text</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="bb-text" class="form-control" value="<?= $p_result["black_box_text"] ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="b-link">Button Link</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="b-link" class="form-control" value="<?= $p_result["button_link"] ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="b-text">Button Text</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="b-text" class="form-control" value="<?= $p_result["button_text"] ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <h5>Chinese</h5>
                                            <div class="row g-6">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="p-text-cn">Text CN</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="p-text-cn" class="form-control" value="<?= $p_result["text_cn"] ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="p-sm-text-cn">Small Text CN</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="p-sm-text-cn" class="form-control" value="<?= $p_result["small_text_cn"] ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="bb-text-cn">Black Box Text CN</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="bb-text-cn" class="form-control" value="<?= $p_result["black_box_text_cn"] ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="b-text-cn">Button Text CN</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="b-text-cn" class="form-control" value="<?= $p_result["button_text_cn"] ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-6">
                                                <div class="col-md-6">
                                                    <div class="row justify-content-end">
                                                        <div class="col-sm-9">
                                                            <button type="button" class="btn btn-primary me-4 submit_poster">Submit</button>
                                                            <button type="reset" class="btn btn-label-secondary">Cancel</button>
                                                        </div>
                                                    </div>
                                                </div>
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
<!-- Edit Banner Modal -->
<div class="modal fade" id="editBanner" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-cat">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2 banner-title"></h4>
                </div>
                <form id="editBannerForm" class="row g-6" onsubmit="return false">
                    <div class="col-6">
                        <label class="form-label" for="banner_link">Link</label>
                        <input type="text" id="banner_link" name="banner_link" class="form-control" placeholder="category" />
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="banner_img">Image</label>
                        <input type="file" class="form-control" accept="image/*" id="banner_img"  />
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary me-3 submit_banner">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Edit Banner Modal -->

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
            $('.slider-title').html("Add New Slider");
            $("#slider_link").val('');
            $("#slider_photo").val(''); // Clear slider photo input
            $("#editSliderForm").data("action", "insert"); // Set action to insert
            $("#slider_img").val(''); // Clear any previous image input
        });

        // Handle Edit Slider
        $(document).on('click', '.edit-slider', function () {
            $('.slider-title').html("Edit Slider");
            const id = $(this).data('id');
            const slider_link = $(this).data('slider_link');
            const slider_photo = $(this).data('slider_photo');

            $("#slider_link").val(slider_link);
            $("#slider_photo").val(slider_photo);
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
            formData.append("slider_img", $("#slider_img")[0].files[0]); // Get the first file selected
            formData.append("type", 'slider');
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

    // Open modal for adding new language
        $(".add_banner").on('click', function () {
            $('.banner-title').html("Add New Banner");
            $("#banner_link").val('');
            $("#editBannerForm").data("action", "insert"); // Set action to insert
        });
        // Handle About Update
        $(document).on('click', '.edit-banner', function () {
            $('.banner-title').html("Edit Banner");
            const id = $(this).data('id');
            const banner_link = $(this).data('banner_link');
            const banner_photo = $(this).data('banner_photo');

            $("#banner_link").val(banner_link);
            $("#banner_photo").val(banner_photo);
            $("#editBannerForm").data("action", "update").data("id", id); // Set action to update and store ID
            $('#editBanner').modal('show');
        });
        $("#editBannerForm").on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            let action = $(this).data("action");
            let id = $(this).data("id");

            // Create a new FormData object
            let formData = new FormData();
            formData.append("banner_link", $("#banner_link").val());
            formData.append("banner_img", $("#banner_img")[0].files[0]); // Get the first file selected
            formData.append("type", 'banner');
            formData.append("action", action);
            formData.append("id", id);

            $.ajax({
                url: '../functions/slider/process_slider.php', // PHP file to handle the request
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content type
                success: function (response) {
                    $('#editBanner').modal('hide');
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
                    $('#editBanner').modal('hide');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: "An error occurred: " + error,
                        showConfirmButton: true
                    });
                }
            });
        });
    $(".submit_poster").on('click', function (e) {
        e.preventDefault(); // Default form submission is prevented
        let formData = {
            text: $("#p-text").val(),
            sm_text : $("#p-sm-text").val(),
            bb_text : $("#bb-text").val(),
            b_link : $("#b-link").val(),
            b_text : $("#b-text").val(),
            text_cn: $("#p-text-cn").val(),
            sm_text_cn : $("#p-sm-text-cn").val(),
            bb_text_cn : $("#bb-text-cn").val(),
            b_link_cn : $("#b-link-cn").val(),
            b_text_cn : $("#b-text-cn").val(),
            action : 'update',
            type : 'poster'
        };

        $.ajax({
            url: '../functions/slider/process_slider.php', // PHP file to handle the request
            type: 'POST',
            data: formData,
            success: function (response) {
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
                $('#editLang').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "An error occurred: " + error,
                    showConfirmButton: true
                });
            }
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