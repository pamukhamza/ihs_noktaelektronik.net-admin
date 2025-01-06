<?php
include_once '../functions/db.php';
require '../functions/admin_template.php';

$database = new Database();

$currentPage = 'brands';
$template = new Template('Markalar - NEBSİS Admin', $currentPage);

// head'i çağırıyoruz
$template->head();
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
                                <table id="cat_table" class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Sürükle</th>
                                        <th>Sıra</th>
                                        <th>Marka Adı</th>
                                    </tr>
                                    </thead>
                                    <tbody id="sortable">
                                    <?php
                                        $query = "SELECT * FROM nokta_urun_markalar ORDER BY order_by";
                                        $results = $database->fetchAll($query);

                                        foreach ($results as $row) {
                                            ?>
                                            <tr data-id="<?= $row['id']; ?>">
                                                <td><i class="fa-solid fa-arrows-up-down-left-right"></i></td>
                                                <td><?= $row['order_by']; ?></td>
                                                <td><?= $row['title']; ?></td>
                                            </tr>
                                            <?php
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
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
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Main JS -->
<script src="../assets/js/main.js"></script>
<script>
    $(document).ready(function() {
        $("#sortable").sortable({
            update: function(event, ui) {
                // Get the updated order of items
                var newOrder = $(this).sortable('toArray', {attribute: 'data-id'}).map(Number);

                // Send updated order to server
                $.ajax({
                    url: '../functions/brands/update_order.php', // Create this PHP file for handling the update
                    type: 'POST',
                    data: {newOrder: newOrder},
                    success: function(response) {
                        // Optionally handle response from server
                        location.reload();
                    }
                });
            }
        });
    });
</script>

