<?php
include_once '../../functions/db.php';
require '../functions/admin_template.php';

$database = new Database();

$currentPage = 'pages';
$template = new Template('Sayfalar - Lahora Admin', $currentPage);

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
                        <h6 class="mt-6"> Edit Pages </h6>
                        <div class="card mb-6">
                            <div class="card-header px-0 pt-0">
                                <div class="nav-align-top">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#form-tabs-about" aria-controls="form-tabs-about" role="tab" aria-selected="true"><span class="ti ti-user ti-lg d-sm-none"></span><span class="d-none d-sm-block">About</span></button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#form-tabs-contact" aria-controls="form-tabs-contact" role="tab" aria-selected="false"><span class="ti ti-phone ti-lg d-sm-none"></span><span class="d-none d-sm-block">Contact</span></button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#form-tabs-policy" aria-controls="form-tabs-policy" role="tab" aria-selected="false"><span class="ti ti-edit ti-lg d-sm-none"></span><span class="d-none d-sm-block">Privacy Policy</span></button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <!-- About -->
                                    <div class="tab-pane fade active show" id="form-tabs-about" role="tabpanel">
                                        <form>
                                            <?php
                                            $query = "SELECT * FROM about";
                                            $a_result = $database->fetch($query);
                                            ?>
                                            <div class="row g-6">
                                                <h6>English</h6>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="about_banner_title">Banner title</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="about_banner_title" class="form-control" value="<?= $a_result["banner_title"]; ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="about_title">Main Title</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="about_title" class="form-control" value="<?= $a_result["title"]; ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="about_banner_title2">Banner second title</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="about_banner_title2" class="form-control" value="<?= $a_result["banner_title2"]; ?>"  />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="about_message">Message</label>
                                                        <div class="col-sm-9">
                                                            <textarea id="about_message" rows="5" class="form-control" aria-describedby="basic-icon-default-message2"><?= $a_result["text"]; ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row g-6">
                                                <h6>Chinese</h6>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="about_banner_title_cn">Banner title</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="about_banner_title_cn" class="form-control" value="<?= $a_result["banner_title_cn"]; ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="about_title_cn">Main Title</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="about_title_cn" class="form-control" value="<?= $a_result["title_cn"]; ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="about_banner_title2_cn">Banner second title</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="about_banner_title2_cn" class="form-control" value="<?= $a_result["banner_title2_cn"]; ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="about_message_cn">Message</label>
                                                        <div class="col-sm-9">
                                                            <textarea id="about_message_cn" rows="5" class="form-control" aria-describedby="basic-icon-default-message2"><?= $a_result["text_cn"]; ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-6">
                                                <div class="col-md-6">
                                                    <div class="row justify-content-end">
                                                        <div class="col-sm-9">
                                                            <button type="button" class="btn btn-primary me-4 submit_about">Submit</button>
                                                            <button type="reset" class="btn btn-label-secondary">Cancel</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- Contact Details -->
                                    <div class="tab-pane fade" id="form-tabs-contact" role="tabpanel">
                                        <?php
                                        $query = "SELECT * FROM contact";
                                        $c_result = $database->fetch($query);
                                        ?>
                                        <form>
                                            <div class="row g-6">
                                                <h6>English</h6>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="contact_w_hours">Working Hours / Days</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="contact_w_hours" class="form-control" value="<?= $c_result["w_hours"]; ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="contact_phone">Phone No</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="contact_phone" class="form-control phone-mask" value="<?= $c_result["phone"]; ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="contact_email">Email</label>
                                                        <div class="col-sm-9">
                                                            <div class="input-group input-group-merge">
                                                                <input type="text" id="contact_email" class="form-control" value="<?= $c_result["email"]; ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="contact_address">Address</label>
                                                        <div class="col-sm-9">
                                                            <textarea name="collapsible-address" class="form-control" id="contact_address" rows="5"><?= $c_result["address"]; ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row g-6">
                                                <h6>Chinese</h6>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="contact_w_hours_cn">Working Hours / Days</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="contact_w_hours_cn" class="form-control" value="<?= $c_result["w_hours_cn"]; ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="contact_address_cn">Address</label>
                                                        <div class="col-sm-9">
                                                            <textarea name="collapsible-address" class="form-control" id="contact_address_cn" rows="5"><?= $c_result["address_cn"]; ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-6">
                                                <div class="col-md-6">
                                                    <div class="row justify-content-end">
                                                        <div class="col-sm-9">
                                                            <button type="submit" class="btn btn-primary me-4 submit_contact">Submit</button>
                                                            <button type="reset" class="btn btn-label-secondary">Cancel</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- Policy -->
                                    <div class="tab-pane fade" id="form-tabs-policy" role="tabpanel">
                                        <?php
                                        $query = "SELECT * FROM policy";
                                        $p_result = $database->fetch($query);
                                        ?>
                                        <form>
                                            <div class="row g-6">
                                                <h6>English</h6>
                                                <div class="col-md-6">
                                                    <div class="mb-6">
                                                        <div class="row">
                                                            <label class="col-sm-3 col-form-label text-sm-end" for="policy_banner">Banner title</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" id="policy_banner" class="form-control" value="<?= $p_result["banner"]; ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-6">
                                                        <div class="row">
                                                            <label class="col-sm-3 col-form-label text-sm-end" for="policy_title">Main Title</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" id="policy_title" class="form-control" value="<?= $p_result["title"]; ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-6">
                                                        <div class="row">
                                                            <label class="col-sm-3 col-form-label text-sm-end" for="policy_text">Message</label>
                                                            <div class="col-sm-9">
                                                                <textarea id="policy_text" class="form-control" rows="5"><?= $p_result["text"]; ?></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row g-6">
                                                <h6>Chinese</h6>
                                                <div class="col-md-6">
                                                    <div class="mb-6">
                                                        <div class="row">
                                                            <label class="col-sm-3 col-form-label text-sm-end" for="policy_banner_cn">Banner title</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" id="policy_banner_cn" class="form-control" value="<?= $p_result["banner_cn"]; ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-6">
                                                        <div class="row">
                                                            <label class="col-sm-3 col-form-label text-sm-end" for="policy_title_cn">Main Title</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" id="policy_title_cn" class="form-control" value="<?= $p_result["title_cn"]; ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-6">
                                                        <div class="row">
                                                            <label class="col-sm-3 col-form-label text-sm-end" for="policy_text_cn">Message</label>
                                                            <div class="col-sm-9">
                                                                <textarea id="policy_text_cn" class="form-control" rows="5"><?= $p_result["text_cn"]; ?></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-6">
                                                <div class="col-md-6">
                                                    <div class="row justify-content-end">
                                                        <div class="col-sm-9">
                                                            <button type="submit" class="btn btn-primary me-4 submit_policy">Submit</button>
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
        // Handle About Update
        $(".submit_about").on('click', function (e) {
            e.preventDefault(); // Prevent default button behavior

            let formData = {
                about_title: $("#about_title").val(),
                about_title_cn: $("#about_title_cn").val(),
                about_banner: $("#about_banner_title").val(),
                about_banner_cn: $("#about_banner_title_cn").val(),
                about_banner2: $("#about_banner_title2").val(),
                about_banner2_cn: $("#about_banner_title2_cn").val(),
                about_message: $("#about_message").val(),
                about_message_cn: $("#about_message_cn").val(),
                action: 'about'
            };

            $.ajax({

                url: '../functions/pages/process_pages.php', // PHP file to handle the request
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
        // Handle Contact Update
        $(".submit_contact").on('click', function (e) {
            e.preventDefault(); // Default form submission is prevented
            let formData = {
                contact_w_hours: $("#contact_w_hours").val(),
                contact_w_hours_cn: $("#contact_w_hours_cn").val(),
                contact_phone: $("#contact_phone").val(),
                contact_email: $("#contact_email").val(),
                contact_address: $("#contact_address").val(),
                contact_address_cn: $("#contact_address_cn").val(),
                action : 'contact'
            };

            $.ajax({
                url: '../functions/pages/process_pages.php', // PHP file to handle the request
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
        // Handle Policy Update
        $(".submit_policy").on('click', function (e) {
            e.preventDefault(); // Default form submission is prevented
            let formData = {
                policy_banner: $("#policy_banner").val(),
                policy_title: $("#policy_title").val(),
                policy_text: $("#policy_text").val(),
                policy_banner_cn: $("#policy_banner_cn").val(),
                policy_title_cn: $("#policy_title_cn").val(),
                policy_text_cn: $("#policy_text_cn").val(),
                action : 'policy'
            };

            $.ajax({
                url: '../functions/pages/process_pages.php', // PHP file to handle the request
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
    });
</script>

</body>
</html>