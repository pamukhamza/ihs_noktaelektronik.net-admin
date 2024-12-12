<?php
include_once '../../functions/db.php';
require '../functions/admin_template.php';

$currentPage = 'blog';
$template = new Template('Blog - Lahora Admin', $currentPage);

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
                                <div class="mb-3">
                                    <button class="btn btn-primary add_blog" data-bs-toggle="modal" data-bs-target="#editBlogModal">Yeni Blog Ekle</button>
                                </div>
                                <table id="lang_table" class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fotoğraf</th>
                                        <th>Başlık</th>
                                        <th>Başlık En</th>
                                        <th>Oluşturma Tarihi</th>
                                        <th>İşlemler</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $database = new Database();
                                    $query = "SELECT * FROM blog";
                                    $results = $database->fetchAll($query);
                                    foreach ($results as $row) {
                                        ?>
                                        <tr>
                                            <td><?= $row['id']; ?></td>
                                            <td><img src="../assets/images/blog/<?= $row['foto']; ?>" alt="Blog Image" style="width: 50px; height: auto;"></td>
                                            <td><?= $row['title']; ?></td>
                                            <td><?= $row['title_cn']; ?></td>
                                            <td><?= $row['create_date']; ?></td>
                                            <td>
                                                <a class="cursor-pointer me-2 edit_blog"
                                                   data-id="<?= $row['id']; ?>"
                                                   data-title="<?= $row['title']; ?>"
                                                   data-title-cn="<?= $row['title_cn']; ?>"
                                                   data-text="<?= $row['text']; ?>"
                                                   data-text-cn="<?= $row['text_cn']; ?>"
                                                   data-foto="<?= $row['foto']; ?>">
                                                    <i class="ti ti-pencil me-1"></i></a>
                                                <a class="cursor-pointer me-2 delete_blog" data-id="<?= $row['id']; ?>"><i class="ti ti-trash me-1"></i></a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit/Add Blog Modal -->
                <div class="modal fade" id="editBlogModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-simple modal-edit-blog">
                        <div class="modal-content">
                            <div class="modal-body">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                <div class="text-center mb-6">
                                    <h4 class="mb-2 modal-title"></h4>
                                </div>
                                <form id="editBlogForm" class="row g-6" onsubmit="return false">
                                    <div class="col-6">
                                        <label class="form-label" for="blogTitle">Başlık</label>
                                        <input type="text" id="blogTitle" name="blogTitle" class="form-control" placeholder="Blog Başlık" required />
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="blogTitleCn">Başlık En</label>
                                        <input type="text" id="blogTitleCn" name="blogTitleCn" class="form-control" placeholder="Blog Başlık En" />
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="blogText">Yazı Alanı</label>
                                        <textarea id="blogText" name="blogText" class="form-control" required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="blogTextCn">Yazı Alanı En</label>
                                        <textarea id="blogTextCn" name="blogTextCn" class="form-control"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="blogFoto">Fotoğraf</label>
                                        <input type="file" id="blogFoto" name="blogFoto" accept="image/*" class="form-control" />
                                    </div>
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-primary me-3">Gönder</button>
                                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">İptal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Edit/Add Blog Modal -->
                <div class="content-backdrop fade"></div>
            </div>
            <?php $template->footer(); ?>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
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

            // Open modal for adding new blog
            $(".add_blog").on('click', function () {
                $('.modal-title').html("Yeni Blog Ekle");
                $("#blogTitle").val('');
                $("#blogTitleCn").val('');
                $("#blogText").val('');
                $("#blogTextCn").val('');
                $("#blogFoto").val('');
                $("#editBlogForm").data("action", "insert");
                $('#editBlogModal').modal('show');
            });

            // Open modal for editing blog
            $(document).on('click', '.edit_blog', function () {
                $('.modal-title').html("BLog Düzenle");
                const id = $(this).data('id');
                const title = $(this).data('title');
                const titleCn = $(this).data('title-cn');
                const text = $(this).data('text');
                const textCn = $(this).data('text-cn');

                $("#blogTitle").val(title);
                $("#blogTitleCn").val(titleCn);
                $("#blogText").val(text);
                $("#blogTextCn").val(textCn);
                $("#editBlogForm").data("action", "update").data("id", id);
                $('#editBlogModal').modal('show');
            });

            // Handle form submission for both add and edit
            $("#editBlogForm").on('submit', function (e) {
                e.preventDefault();

                const action = $(this).data("action");
                const formData = new FormData(this);
                formData.append('action', action);
                formData.append("foto", $("#blogFoto")[0].files[0]);
                formData.append("title", $("#blogTitle").val());
                formData.append("titleCn", $("#blogTitleCn").val());
                formData.append("text", $("#blogText").val());
                formData.append("textCn", $("#blogTextCn").val());

                if (action === "update") {
                    formData.append('id', $(this).data("id"));
                }

                $.ajax({
                    url: '../functions/blog/process_blog.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $('#editBlogModal').modal('hide');
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
                        $('#editBlogModal').modal('hide');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: "An error occurred: " + error,
                            showConfirmButton: true
                        });
                    }
                });
            });

            $(".delete_blog").on('click', function () {
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
                            data: { id: id, tablename: 'blog', type: 'delete' },  // Type delete olarak gönderiliyor
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
