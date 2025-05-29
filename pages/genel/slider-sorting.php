<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$database = new Database();
$currentPage = 'slider';
$template = new Template('Slider Sıralama - NEBSİS', $currentPage);

// Get site type from URL parameter
$siteType = isset($_GET['site']) ? $_GET['site'] : 'b2b';
$validSites = ['b2b', 'net', 'b2b-urun'];
if (!in_array($siteType, $validSites)) {
    $siteType = 'b2b';
}

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
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="btn-group" role="group">
                            <a href="pages/genel/slider-sorting?site=b2b" class="btn btn-outline-primary <?= $siteType === 'b2b' ? 'active' : '' ?>">B2B</a>
                            <a href="pages/genel/slider-sorting?site=net" class="btn btn-outline-primary <?= $siteType === 'net' ? 'active' : '' ?>">NET</a>
                            <a href="pages/genel/slider-sorting?site=b2b-urun" class="btn btn-outline-primary <?= $siteType === 'b2b-urun' ? 'active' : '' ?>">B2B Ürün</a>
                        </div>
                        <div class="mb-3">
                            <a href="pages/genel/slider" class="btn btn-primary">Slider</a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xxl-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <table id="cat_table" class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Sürükle</th>
                                        <th>Sıra</th>
                                        <th>Link</th>
                                        <th>Görsel</th>
                                    </tr>
                                    </thead>
                                    <tbody id="sortable">
                                    <?php
                                        $query = "SELECT * FROM slider WHERE site = :site_type ORDER BY order_by";
                                        $params = ["site_type" => $siteType];
                                        $results = $database->fetchAll($query, $params);
                                        foreach ($results as $row) {
                                            ?>
                                            <tr data-id="<?= $row['id']; ?>">
                                                <td><i class="fa-solid fa-arrows-up-down-left-right"></i></td>
                                                <td><?= $row['order_by']; ?></td>
                                                <td><?= $row['link']; ?></td>
                                                <td>
                                                    <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/slider/<?= $row["photo"] ?>" alt="photo" width="150px" class="">
                                                </td>
                                            </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
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
<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/main.js"></script>
<script>
    $(document).ready(function() {
        $("#sortable").sortable({
            update: function(event, ui) {
                var newOrder = $(this).sortable('toArray', {attribute: 'data-id'}).map(Number);

                $.ajax({
                    url: 'functions/slider/update_sort.php?site=<?= $siteType ?>', // Add site parameter
                    type: 'POST',
                    data: {newOrder: newOrder},
                    success: function(response) {
                        location.reload();
                    }
                });
            }
        });
    });
</script>
</body>
</html>