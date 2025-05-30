<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$database = new Database();

$currentPage = 'b2b-banner';
$template = new Template('B2B Banner - Nokta Admin', $currentPage);

$template->head();
?>
<link rel="stylesheet" href="assets/css/switch.css">
<body>

<div class="layout-wrapper layout-content-navbar  ">
    <div class="layout-container">
        <?php $template->header(); ?>
        <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="row">
                    <div class="col">
                        <div class="card mb-6">
                            <div class="card-header">B2B Bannerlar</div>
                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <div class="tab-pane fade active show" id="form-tabs-net" role="tabpanel">
                                        <div class="table-responsive text-nowrap">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Link</th>
                                                        <th>Photo</th>
                                                        <th>Ölçüler</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="table-border-bottom-0">
                                                    <?php
                                                        $database = new Database();
                                                        $query = "SELECT * FROM b2b_banner ";
                                                        $results = $database->fetchAll($query);
                                                        foreach ($results as $row) {
                                                    ?>
                                                        <tr>
                                                            <td><?= $row["id"] ?></td>
                                                            <td style="max-width: 650px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $row["banner_link"] ?></td>
                                                            <td>
                                                                <ul class="list-unstyled m-0 avatar-group d-flex align-items-center">
                                                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top">
                                                                        <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/<?= $row["banner_foto"] ?>" alt="photo" width="150px" class="">
                                                                    </li>
                                                                </ul>
                                                            </td>
                                                            <td><?= $row["lang"] ?></td>
                                                            <td>
                                                                <label class="switch switch-success">
                                                                    <input type="checkbox" class="switch-input active-checkbox-banner" data-id="<?= $row['id']; ?>" <?= $row['aktif'] == 1 ? 'checked' : ''; ?> />
                                                                    <span class="switch-toggle-banner">
                                                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                                                    </span>
                                                                    <span class="switch-label"></span>
                                                                </label>
                                                            </td>
                                                            <td>
                                                                <a class="cursor-pointer me-2 edit-banner"
                                                                data-id="<?= $row["id"] ?>"
                                                                data-banner_link="<?= $row['banner_link']; ?>"
                                                                data-banner_photo="<?= $row['banner_foto']; ?>"><i class="ti ti-pencil me-1"></i></a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card mb-6">
                            <div class="card-header">B2B Bannerlar</div>
                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <div class="tab-pane fade active show" id="form-tabs-net" role="tabpanel">
                                        <div class="table-responsive text-nowrap">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Link1</th>
                                                        <th>Photo1</th>
                                                        <th>Link2</th>
                                                        <th>Photo2</th>
                                                        <th>Link3</th>
                                                        <th>Photo3</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="table-border-bottom-0">
                                                    <?php
                                                        $database = new Database();
                                                        $query = "SELECT * FROM banner_modal WHERE aktif = 1 ";
                                                        $results = $database->fetchAll($query);
                                                        foreach ($results as $row) {
                                                    ?>
                                                        <tr>
                                                            <td><?= $row["id"] ?></td>
                                                            <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $row["link1"] ?></td>
                                                            <td>
                                                                <ul class="list-unstyled m-0 avatar-group d-flex align-items-center">
                                                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top">
                                                                        <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/<?= $row["foto1"] ?>" alt="photo" width="150px" class="">
                                                                    </li>
                                                                </ul>
                                                            </td>
                                                            <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $row["link2"] ?></td>
                                                            <td>
                                                                <ul class="list-unstyled m-0 avatar-group d-flex align-items-center">
                                                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top">
                                                                        <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/<?= $row["foto2"] ?>" alt="photo" width="150px" class="">
                                                                    </li>
                                                                </ul>
                                                            </td>
                                                            <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $row["link3"] ?></td>
                                                            <td>
                                                                <ul class="list-unstyled m-0 avatar-group d-flex align-items-center">
                                                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top">
                                                                        <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/<?= $row["foto3"] ?>" alt="photo" width="150px" class="">
                                                                    </li>
                                                                </ul>
                                                            </td>
                                                            <td>
                                                                <a class="cursor-pointer me-2 edit-banner-modal"
                                                                data-id="<?= $row["id"] ?>"
                                                                data-banner_link1="<?= $row['link1']; ?>"
                                                                data-banner_photo1="<?= $row['foto1']; ?>"
                                                                data-banner_link2="<?= $row['link2']; ?>"
                                                                data-banner_photo2="<?= $row['foto2']; ?>"
                                                                data-banner_link3="<?= $row['link3']; ?>"
                                                                data-banner_photo3="<?= $row['foto3']; ?>"
                                                                ><i class="ti ti-pencil me-1"></i></a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
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
                        <input type="text" id="banner_link" name="banner_link" class="form-control" placeholder="link" />
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="banner_img">Image</label>
                        <input type="file" class="form-control" accept="image/*" id="banner_img" />
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

<!-- Edit Banner Modal Modal -->
<div class="modal fade" id="editBannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-cat">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2 banner-modal-title">Banner Modal Düzenle</h4>
                </div>
                <form id="editBannerModalForm" class="row g-6" onsubmit="return false">
                    <div class="col-6">
                        <label class="form-label" for="banner_link1">Link 1</label>
                        <input type="text" id="banner_link1" name="banner_link1" class="form-control" placeholder="link 1" />
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="banner_img1">Image 1</label>
                        <input type="file" class="form-control" accept="image/*" id="banner_img1" />
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="banner_link2">Link 2</label>
                        <input type="text" id="banner_link2" name="banner_link2" class="form-control" placeholder="link 2" />
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="banner_img2">Image 2</label>
                        <input type="file" class="form-control" accept="image/*" id="banner_img2" />
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="banner_link3">Link 3</label>
                        <input type="text" id="banner_link3" name="banner_link3" class="form-control" placeholder="link 3" />
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="banner_img3">Image 3</label>
                        <input type="file" class="form-control" accept="image/*" id="banner_img3" />
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary me-3 submit_banner_modal">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Edit Banner Modal Modal -->

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
        $(document).on('click', '.edit-banner', function () {
            $('.banner-title').html("Banner Düzenle");
            const id = $(this).data('id');
            const banner_link = $(this).data('banner_link');
            const banner_photo = $(this).data('banner_photo');

            $("#banner_link").val(banner_link);
            $("#banner_photo").val(banner_photo);
            $("#editBannerForm").data("action", "update").data("id", id); // Set action to update and store ID
            $('#editBanner').modal('show');
        });

        // Form Submission
        $("#editBannerForm").on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            let action = $(this).data("action");
            let id = $(this).data("id");

            let formData = new FormData();
            formData.append("banner_link", $("#banner_link").val());
            formData.append("banner_img", $("#banner_img")[0].files[0]);
            formData.append("action", action);
            formData.append("id", id);

            // AJAX Request
            $.ajax({
                url: 'functions/banner/process_banner.php', // PHP file to handle the request
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

        // Banner Modal Edit
        $(document).on('click', '.edit-banner-modal', function () {
            const id = $(this).data('id');
            const banner_link1 = $(this).data('banner_link1');
            const banner_photo1 = $(this).data('banner_photo1');
            const banner_link2 = $(this).data('banner_link2');
            const banner_photo2 = $(this).data('banner_photo2');
            const banner_link3 = $(this).data('banner_link3');
            const banner_photo3 = $(this).data('banner_photo3');

            $("#banner_link1").val(banner_link1);
            $("#banner_link2").val(banner_link2);
            $("#banner_link3").val(banner_link3);
            $("#editBannerModalForm").data("action", "update").data("id", id);
            $('#editBannerModal').modal('show');
        });

        // Banner Modal Form Submission
        $("#editBannerModalForm").on('submit', function (e) {
            e.preventDefault();

            let action = $(this).data("action");
            let id = $(this).data("id");

            let formData = new FormData();
            formData.append("banner_link1", $("#banner_link1").val());
            formData.append("banner_link2", $("#banner_link2").val());
            formData.append("banner_link3", $("#banner_link3").val());
            formData.append("banner_img1", $("#banner_img1")[0].files[0]);
            formData.append("banner_img2", $("#banner_img2")[0].files[0]);
            formData.append("banner_img3", $("#banner_img3")[0].files[0]);
            formData.append("action", action);
            formData.append("id", id);

            $.ajax({
                url: 'functions/banner/process_banner_modal.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#editBannerModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response,
                        showConfirmButton: false,
                        timer: 1000
                    }).then(function() {
                        location.reload();
                    });
                },
                error: function (xhr, status, error) {
                    $('#editBannerModal').modal('hide');
                    console.error("AJAX Error: ", xhr, status, error);
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
    $('.active-checkbox-banner').on('change', function() {
        var id = $(this).data('id');
        var activeStatus = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: 'functions/update_status.php',  // PHP dosyanızın ismini yazın
            type: 'POST',
            data: {
                id: id,
                field: 'aktif',
                value: activeStatus,
                database: 'b2b_banner'
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