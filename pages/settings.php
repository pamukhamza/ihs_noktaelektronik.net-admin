<?php
include_once '../../functions/db.php';
require '../functions/admin_template.php';

$database = new Database();

$currentPage = 'settings';
$template = new Template('Ayarlar - Lahora Admin', $currentPage);

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
                                            <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#form-tabs-settings" aria-controls="form-tabs-settings" role="tab" aria-selected="true"><span class="ti ti-settings ti-lg d-sm-none"></span><span class="d-none d-sm-block">Ayarlar</span></button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#form-tabs-social" aria-controls="form-tabs-social" role="tab" aria-selected="false"><span class="ti ti-link ti-lg d-sm-none"></span><span class="d-none d-sm-block">Sosyal Medya Linkleri</span></button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <!-- Personal Info -->
                                    <div class="tab-pane fade active show" id="form-tabs-settings" role="tabpanel">
                                        <form method="post" enctype="multipart/form-data">
                                            <?php
                                            $query = "SELECT * FROM settings";
                                            $s_result = $database->fetch($query);
                                            ?>
                                            <div class="row g-6">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="site-title">Site Title</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="site-title" class="form-control" value="<?= $s_result["site_title"] ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="favicon_img">Favicon</label>
                                                        <div class="col-sm-9">
                                                            <input type="file" class="form-control" accept="image/*" id="favicon_img" required />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-6">
                                                <div class="col-md-6">
                                                    <div class="row justify-content-end">
                                                        <div class="col-sm-9">
                                                            <button type="button" class="btn btn-primary me-4 submit_settings">Gönder</button>
                                                            <button type="reset" class="btn btn-label-secondary">İptal</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- Social Links -->
                                    <div class="tab-pane fade" id="form-tabs-social" role="tabpanel">
                                        <form>
                                            <div class="row g-6">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="twitter">Twitter</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="twitter" class="form-control" value="<?= $s_result["twitter"] ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="facebook">Facebook</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="facebook" class="form-control" value="<?= $s_result["facebook"] ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="linkedin">Linkedin</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="linkedin" class="form-control" value="<?= $s_result["linkedin"] ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="instagram">Instagram</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="instagram" class="form-control" value="<?= $s_result["instagram"] ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="youtube">Youtube</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="youtube" class="form-control" value="<?= $s_result["youtube"] ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-6">
                                                <div class="col-md-6">
                                                    <div class="row justify-content-end">
                                                        <div class="col-sm-9">
                                                            <button type="button" class="btn btn-primary me-4 submit_settings2">Gönder</button>
                                                            <button type="reset" class="btn btn-label-secondary">İptal</button>
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
<!-- Core JS -->
<!-- build:js ../assets/vendor/js/core.js -->

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
        // Handle Contact Update
        $(".submit_settings").on('click', function (e) {
            e.preventDefault(); // Prevent default form submission

            // Create a new FormData object
            let formData = new FormData();
            formData.append("site_title", $("#site-title").val());
            formData.append("favicon_img", $("#favicon_img")[0].files[0]); // Get the first file selected
            formData.append("action", 'settings');

            $.ajax({
                url: '../functions/settings/process_settings.php', // PHP file to handle the request
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content type
                success: function (response) {
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
                    $('#editLang').modal('hide');
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: "Bir hata oluştu: " + error,
                        showConfirmButton: true
                    });
                }
            });
        });
        // Handle Policy Update
        $(".submit_settings2").on('click', function (e) {
            e.preventDefault(); // Default form submission is prevented
            let formData = {
                twitter: $("#twitter").val(),
                facebook: $("#facebook").val(),
                instagram: $("#instagram").val(),
                linkedin: $("#linkedin").val(),
                youtube: $("#youtube").val(),
                action : 'socials'
            };

            $.ajax({
                url: '../functions/settings/process_settings.php', // PHP file to handle the request
                type: 'POST',
                data: formData,
                success: function (response) {
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
                    $('#editLang').modal('hide');
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: "Bir hata oluştu: " + error,
                        showConfirmButton: true
                    });
                }
            });
        });
    });
</script>
</body>
</html>