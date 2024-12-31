<?php
include_once '../functions/db.php';
require '../functions/admin_template.php';

$database = new Database();

$currentPage = 'documents';
$template = new Template('Documents - Nokta Admin', $currentPage);

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
                                            <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#form-tabs-kvkk" aria-controls="form-tabs-kvkk" role="tab" aria-selected="true"><span class="ti ti-user ti-lg d-sm-none"></span><span class="d-none d-sm-block">KVKK</span></button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#form-tabs-gss" aria-controls="form-tabs-gss" role="tab" aria-selected="false"><span class="ti ti-phone ti-lg d-sm-none"></span><span class="d-none d-sm-block">Gizlilik Sözleşmesi</span></button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#form-tabs-ss" aria-controls="form-tabs-ss" role="tab" aria-selected="false"><span class="ti ti-edit ti-lg d-sm-none"></span><span class="d-none d-sm-block">Satış Sözleşmesi</span></button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <!-- kvkk -->
                                    <div class="tab-pane fade active show" id="form-tabs-kvkk" role="tabpanel">
                                        <div class="mb-3">
                                            <button class="btn btn-primary add_slider" data-bs-toggle="modal" data-bs-target="#editSlider">Add New</button>
                                        </div>
                                        <form>
                                            <div class="table-responsive text-nowrap">
                                                <table class="table table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Title</th>
                                                        <th>Site</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="table-border-bottom-0">
                                                    <?php
                                                    $database = new Database();
                                                    $query = "SELECT * FROM documents WHERE type = 'kvkk'";
                                                    $results = $database->fetchAll($query);
                                                    foreach ($results as $row) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $row["id"] ?></td>
                                                        <td><?= $row["title"] ?></td>
                                                        <td><?= $row["site"] ?></td>
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
                                                               data-slider_title="<?= $row['title']; ?>"
                                                               data-slider_text="<?= $row['text']; ?>"
                                                               data-slider_site="<?= $row['site']; ?>"><i class="ti ti-pencil me-1"></i></a>
                                                            <a class="cursor-pointer delete_slider" data-id="<?= $row['id']; ?>"><i class="ti ti-trash me-1"></i></a>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- GSS -->
                                    <div class="tab-pane fade" id="form-tabs-gss" role="tabpanel">
                                        <div class="mb-3">
                                            <button class="btn btn-primary add_gss" data-bs-toggle="modal" data-bs-target="#editGSS">Add New</button>
                                        </div>
                                        <form>
                                            <div class="table-responsive text-nowrap">
                                                <table class="table table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Title</th>
                                                        <th>Site</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="table-border-bottom-0">
                                                    <?php
                                                    $database = new Database();
                                                    $query = "SELECT * FROM documents WHERE type = 'gss'";
                                                    $results = $database->fetchAll($query);
                                                    foreach ($results as $row) {
                                                        ?>
                                                        <tr>
                                                            <td><?= $row["id"] ?></td>
                                                            <td><?= $row["title"] ?></td>
                                                            <td><?= $row["site"] ?></td>
                                                            <td>
                                                                <label class="switch switch-success">
                                                                    <input type="checkbox" class="switch-input active-checkbox-gss" data-id="<?= $row['id']; ?>" <?= $row['status'] == 1 ? 'checked' : ''; ?> />
                                                                    <span class="switch-toggle-slider">
                                                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                                                    </span>
                                                                    <span class="switch-label"></span>
                                                                </label>
                                                            </td>
                                                            <td>
                                                                <a class="cursor-pointer me-2 edit-gss"
                                                                   data-id="<?= $row["id"] ?>"
                                                                   data-gss_title="<?= $row["title"] ?>"
                                                                   data-gss_text="<?= $row["text"] ?>"
                                                                   data-gss_site="<?= $row["site"] ?>"><i class="ti ti-pencil me-1"></i></a>
                                                                <a class="cursor-pointer delete_slider" data-id="<?= $row['id']; ?>"><i class="ti ti-trash me-1"></i></a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- SS -->
                                    <div class="tab-pane fade" id="form-tabs-ss" role="tabpanel">
                                        <div class="mb-3">
                                            <button class="btn btn-primary add_ss" data-bs-toggle="modal" data-bs-target="#editSS">Add New</button>
                                        </div>
                                        <form>
                                            <div class="table-responsive text-nowrap">
                                                <table class="table table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Title</th>
                                                        <th>Site</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="table-border-bottom-0">
                                                    <?php
                                                    $database = new Database();
                                                    $query = "SELECT * FROM documents WHERE type = 'ss'";
                                                    $results = $database->fetchAll($query);
                                                    foreach ($results as $row) {
                                                        ?>
                                                        <tr>
                                                            <td><?= $row["id"] ?></td>
                                                            <td><?= $row["title"] ?></td>
                                                            <td><?= $row["site"] ?></td>
                                                            <td>
                                                                <label class="switch switch-success">
                                                                    <input type="checkbox" class="switch-input active-checkbox-ss" data-id="<?= $row['id']; ?>" <?= $row['status'] == 1 ? 'checked' : ''; ?> />
                                                                    <span class="switch-toggle-slider">
                                                                    <span class="switch-on"><i class="ti ti-check"></i></span>
                                                                    <span class="switch-off"><i class="ti ti-x"></i></span>
                                                                </span>
                                                                    <span class="switch-label"></span>
                                                                </label>
                                                            </td>
                                                            <td>
                                                                <a class="cursor-pointer me-2 edit-ss"
                                                                    data-id="<?= $row["id"] ?>"
                                                                    data-ss_title="<?= $row["title"] ?>"
                                                                    data-ss_text="<?= $row["text"] ?>"
                                                                    data-ss_site="<?= $row["site"] ?>"><i class="ti ti-pencil me-1"></i></a>
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
<!-- KVKK Modal -->
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
                        <label class="form-label" for="slider_title">Title</label>
                        <input type="text" id="slider_title" name="slider_title" class="form-control"/>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="slider_text">Text</label>
                        <textarea type="text" id="slider_text" name="slider_text" class="form-control"></textarea>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="slider_site">Site</label>
                        <select id="slider_site" name="slider_site" class="form-select">
                            <option value="net">net</option>
                            <option value="b2b">b2b</option>
                            <option value="netcn">netcn</option>
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
<!--/ KVKK Modal -->
<!-- GSS Modal -->
<div class="modal fade" id="editGSS" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-gss">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2 gss-title"></h4>
                </div>
                <form id="editGSSForm" class="row g-6" onsubmit="return false">
                    <div class="col-6">
                        <label class="form-label" for="gss_title">Title</label>
                        <input type="text" id="gss_title" name="gss_title" class="form-control"/>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="gss_text">Text</label>
                        <textarea type="text" id="gss_text" name="gss_text" class="form-control"></textarea>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="gss_site">Site</label>
                        <select id="gss_site" name="gss_site" class="form-select">
                            <option value="net">net</option>
                            <option value="b2b">b2b</option>
                            <option value="netcn">netcn</option>
                        </select>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary me-3 submit_gss">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ GSS Modal -->
<!-- SS Modal -->
<div class="modal fade" id="editSS" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-ss">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2 ss-title"></h4>
                </div>
                <form id="editSSForm" class="row g-6" onsubmit="return false">
                    <div class="col-6">
                        <label class="form-label" for="ss_title">Title</label>
                        <input type="text" id="ss_title" name="ss_title" class="form-control"/>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="ss_text">Text</label>
                        <textarea type="text" id="ss_text" name="ss_text" class="form-control"></textarea>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="ss_site">Site</label>
                        <select id="ss_site" name="ss_site" class="form-select">
                            <option value="net">net</option>
                            <option value="b2b">b2b</option>
                            <option value="netcn">netcn</option>
                        </select>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary me-3 submit_ss">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ SS Modal -->

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
            $('.slider-title').html("KVKK Ekle");
            $("#slider_title").val('');
            $("#slider_text").val('');
            $("#slider_site").val('');
            $("#editSliderForm").data("action", "insert"); // Set action to insert
        });

        // Handle Edit Slider
        $(document).on('click', '.edit-slider', function () {
            $('.slider-title').html("KVKK Düzenle");
            const id = $(this).data('id');
            const slider_title = $(this).data('slider_title');
            const slider_text = $(this).data('slider_text');
            const slider_site = $(this).data('slider_site');

            $("#slider_title").val(slider_title);
            $("#slider_text").val(slider_text);
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
            formData.append("slider_title", $("#slider_title").val());
            formData.append("slider_text", $("#slider_text").val());
            formData.append("slider_site", $("#slider_site").val());
            formData.append("type", 'kvkk');
            formData.append("action", action);
            formData.append("id", id);

            // AJAX Request
            $.ajax({
                url: '../functions/documents/process_documents.php', // PHP file to handle the request
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content type
                success: function (response) {
                    $('#editSlider').modal('hide');
                    alert(response);
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

    $(document).ready(function() {
        // Open modal for adding new slider
        $(".add_gss").on('click', function () {
            $('.gss-title').html("Gizlilik Sözleşmesi Ekle");
            $("#gss_title").val('');
            $("#gss_text").val('');
            $("#gss_site").val('');
            $("#editGSSForm").data("action", "insert"); // Set action to insert
        });

        // Handle Edit Slider
        $(document).on('click', '.edit-gss', function () {
            $('.gss-title').html("Gizlilik Sözleşmesi Düzenle");
            const id = $(this).data('id');
            const gss_title = $(this).data('gss_title');
            const gss_text = $(this).data('gss_text');
            const gss_site = $(this).data('gss_site');

            $("#gss_title").val(gss_title);
            $("#gss_text").val(gss_text);
            $("#gss_site").val(gss_site);
            $("#editGSSForm").data("action", "update").data("id", id); // Set action to update and store ID
            $('#editGSS').modal('show');
        });

        // Form Submission
        $("#editGSSForm").on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            let action = $(this).data("action");
            let id = $(this).data("id");

            let formData = new FormData();
            formData.append("gss_title", $("#gss_title").val());
            formData.append("gss_text", $("#gss_text").val());
            formData.append("gss_site", $("#gss_site").val());
            formData.append("type", 'gss');
            formData.append("action", action);
            formData.append("id", id);

            // AJAX Request
            $.ajax({
                url: '../functions/documents/process_documents.php', // PHP file to handle the request
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content type
                success: function (response) {
                    $('#editGSS').modal('hide');
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
                    $('#editGSS').modal('hide');
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

    $(document).ready(function() {
        // Open modal for adding new slider
        $(".add_ss").on('click', function () {
            $('.ss-title').html("Satış Sözleşmesi Ekle");
            $("#ss_title").val('');
            $("#ss_text").val('');
            $("#ss_site").val('');
            $("#editSSForm").data("action", "insert"); // Set action to insert
        });

        // Handle Edit Slider
        $(document).on('click', '.edit-ss', function () {
            $('.ss-title').html("Satış Sözleşmesi Düzenle");
            const id = $(this).data('id');
            const ss_title = $(this).data('ss_title');
            const ss_text = $(this).data('ss_text');
            const ss_site = $(this).data('ss_site');

            $("#ss_title").val(ss_title);
            $("#ss_text").val(ss_text);
            $("#ss_site").val(ss_site);
            $("#editSSForm").data("action", "update").data("id", id); // Set action to update and store ID
            $('#editSS').modal('show');
        });

        // Form Submission
        $("#editSSForm").on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            let action = $(this).data("action");
            let id = $(this).data("id");

            let formData = new FormData();
            formData.append("ss_title", $("#ss_title").val());
            formData.append("ss_text", $("#ss_text").val());
            formData.append("ss_site", $("#ss_site").val());
            formData.append("type", 'ss');
            formData.append("action", action);
            formData.append("id", id);

            // AJAX Request
            $.ajax({
                url: '../functions/documents/process_documents.php', // PHP file to handle the request
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content type
                success: function (response) {
                    $('#editSS').modal('hide');
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
                    $('#editSS').modal('hide');
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
                    data: { id: id, tablename: 'documents', type: 'delete' },  // Type delete olarak gönderiliyor
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
                database: 'documents'
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
    $('.active-checkbox-gss').on('change', function() {
        var id = $(this).data('id');
        var activeStatus = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: '../functions/update_status.php',  // PHP dosyanızın ismini yazın
            type: 'POST',
            data: {
                id: id,
                field: 'status',
                value: activeStatus,
                database: 'documents'
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
    $('.active-checkbox-ss').on('change', function() {
        var id = $(this).data('id');
        var activeStatus = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: '../functions/update_status.php',  // PHP dosyanızın ismini yazın
            type: 'POST',
            data: {
                id: id,
                field: 'status',
                value: activeStatus,
                database: 'documents'
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