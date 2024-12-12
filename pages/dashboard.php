<?php
require '../functions/admin_template.php';

$currentPage = 'dashboard';
$template = new Template('Dashboard - Lahora Admin', $currentPage);

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
                    <div class="row g-6">
                        <!-- Support Tracker -->
                        <div class="col-md-3">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="card-title mb-0">
                                        <h5 class="mb-1">Support Tracker</h5>
                                    </div>
                                </div>
                                <div class="card-body row">
                                    <div class="col-12 col-sm-4 col-md-12 col-lg-4">
                                        <ul class="p-0 m-0">
                                            <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
                                                <div class="badge rounded bg-label-primary p-1_5"><i class="ti ti-ticket ti-md"></i></div>
                                                <div>
                                                    <h6 class="mb-0 text-nowrap">Total Products</h6>
                                                    <small class="text-muted">-</small>
                                                </div>
                                            </li>
                                            <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
                                                <div class="badge rounded bg-label-info p-1_5"><i class="ti ti-circle-check ti-md"></i></div>
                                                <div>
                                                    <h6 class="mb-0 text-nowrap">Total Blog</h6>
                                                    <small class="text-muted">-</small>
                                                </div>
                                            </li>
                                            <li class="d-flex gap-4 align-items-center pb-1">
                                                <div class="badge rounded bg-label-warning p-1_5"><i class="ti ti-clock ti-md"></i></div>
                                                <div>
                                                    <h6 class="mb-0 text-nowrap">Total Contact Form</h6>
                                                    <small class="text-muted">-</small>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ Support Tracker -->
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
<script src="../assets/vendor/libs/jquery/jquery.js"></script>
<script src="../assets/vendor/js/bootstrap.js"></script>
<script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
<script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="../assets/vendor/libs/i18n/i18n.js"></script>
<script src="../assets/vendor/js/menu.js"></script>
<script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>
<script src="../assets/vendor/libs/swiper/swiper.js"></script>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/dashboards-analytics.js"></script>
</body>
</html>