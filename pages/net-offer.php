<?php
include_once '../functions/db.php';
include_once '../functions/admin_template.php';

$currentPage = 'net-offer';
$template = new Template('Teklifler - NEBSİS - .net', $currentPage);

$template->head();
?>
<body>
<div class="layout-wrapper layout-content-navbar  ">
    <div class="layout-container">
        <?php $template->header(); ?>
        <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="row g-6">
                    <div class="col-xxl-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <table id="lang_table" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Ad</th>
                                            <th>Firma</th>
                                            <th>Telefon</th>
                                            <th>Mail</th>
                                            <th>Tarih</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $database = new Database();
                                            $query = "SELECT * FROM net_offers";
                                            $results = $database->fetchAll($query);
                                            foreach ($results as $row) {
                                        ?>
                                            <tr>
                                                <td><?= $row['name']; ?></td>
                                                <td><?= $row['company']; ?></td>
                                                <td><?= $row['phone']; ?></td>
                                                <td><?= $row['mail']; ?></td>
                                                <td><?= $row['date']; ?></td>
                                                <td>
                                                    <a class="cursor-pointer me-2 view-details" data-id="<?= $row['id']; ?>"><i class="ti ti-eye me-1"></i></a>
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
            <!-- Modal Structure -->
            <div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="dataModalLabel">Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Content will be loaded dynamically -->
                            <div id="modalContent"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/i18n/i18n.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <!-- DataTables CSS ve JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css">

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>
    <script>
        $(document).ready(function() {
            const table = $('#lang_table').DataTable({
                dom: 'Bfrtip',  // Dışa aktarma butonlarını etkinleştir
                buttons: [
                    'excelHtml5'
                ]
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Handle click event for 'view-details' links
            $(document).on('click', '.view-details', function() {
                const id = $(this).data('id'); // Get the ID from the data-id attribute

                // Make an AJAX request to fetch data
                $.ajax({
                    url: '../functions/web_net/offer/fetch_offer_details.php', // Create this PHP file to handle data retrieval
                    type: 'GET',
                    data: { id: id },
                    success: function(response) {
                        // Populate the modal content with the response
                        $('#modalContent').html(response);
                        // Show the modal
                        $('#dataModal').modal('show');
                    },
                    error: function() {
                        alert('Failed to fetch data. Please try again.');
                    }
                });
            });
        });
    </script>

</body>
</html>