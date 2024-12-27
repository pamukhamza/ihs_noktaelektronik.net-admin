<?php
include_once '../functions/db.php';
require '../functions/admin_template.php';

$currentPage = 'users';
$template = new Template('Kullanıcılar - NEBSİS Admin', $currentPage);

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
                    <!-- Projects table -->
                    <div class="col-xxl-12">
                        <!-- DataTable with Buttons -->
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <button class="btn btn-primary add_user" data-bs-toggle="modal" data-bs-target="#editUser">Yeni Ekle</button>
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary user_permissions">Tüm Yetkiler</button>
                                </div>
                                <table id="lang_table" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Kullanıcı Adı</th>
                                            <th>E-Posta</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $database = new Database();
                                            $query = "SELECT * FROM users";
                                            $results = $database->fetchAll($query);
                                            foreach ($results as $row) {
                                        ?>
                                        <tr>
                                            <td><?= $row['id']; ?></td>
                                            <td><?= $row['username']; ?></td>
                                            <td><?= $row['email']; ?></td>
                                            <td>
                                                <a class="cursor-pointer me-2 edit_user" href="users-detail?id=<?= $row['id']; ?>"><i class="ti ti-pencil me-1"></i></a>
                                                <a class="cursor-pointer delete_user" data-id="<?= $row['id']; ?>"><i class="ti ti-trash me-1"></i></a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <!-- Add more rows as needed -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--/ Projects table -->
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

<script>
    $(document).ready(function() {
        const table = $('#lang_table').DataTable();

        // Open modal for adding new language
        $(".add_user").on('click', function () {
            window.location.href = 'users-detail?id=new';
        });
        $(".user_permissions").on('click', function () {
            window.location.href = 'user_permissions';
        });

        $(document).on('click', '.delete_user', function (e) {
            e.preventDefault();
            var langId = $(this).data('id'); // Get the ID from data attribute

            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu işlemi geri alamazsınız!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Evet, sil!',
                cancelButtonText: 'Hayır, iptal et'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../functions/functions.php', 
                        type: 'POST',
                        data: { id: langId, tablename: 'users', type: 'delete' },
                        success: function (response) {
                            Swal.fire(
                                'Silindi!',
                                response,
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        },
                        error: function () {
                            Swal.fire(
                                'Hata!',
                                'Dil silinirken bir hata oluştu.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>
</body>
</html>