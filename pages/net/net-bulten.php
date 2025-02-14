<?php
include_once '../../functions/db.php';
include_once '../../functions/admin_template.php';

$currentPage = 'net-bulten';
$template = new Template('E-Bülten - NEBSİS - .net', $currentPage);

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
                                            <th>Mail</th>
                                            <th>işlem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $database = new Database();
                                        $query = "SELECT * FROM nokta_ebulten WHERE `site` = 'net'";
                                        $results = $database->fetchAll($query);
                                        foreach ($results as $row) {
                                            ?>
                                            <tr>
                                                <td><?= $row['id']; ?></td>
                                                <td><?= $row['email']; ?></td>
                                                <td>
                                                    <a class="cursor-pointer me-2 delete_newsletter" data-id="<?= $row['id']; ?>"><i class="ti ti-trash me-1"></i></a>
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
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="assets/vendor/libs/i18n/i18n.js"></script>
    <script src="assets/vendor/js/menu.js"></script>

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
    <script src="assets/js/main.js"></script>
    <script>
        $(document).ready(function() {
            const table = $('#lang_table').DataTable({
                dom: 'Bfrtip',  // Dışa aktarma butonlarını etkinleştir
                buttons: [
                    'excelHtml5'
                ]
            });
            $(".delete_newsletter").on('click', function () {
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
                            url: 'functions/functions.php',
                            type: 'POST',
                            data: { id: id, tablename: 'nokta_ebulten', type: 'delete' },  // Type delete olarak gönderiliyor
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
        });
    </script>
</body>
</html>