<?php
include_once '../../functions/db.php';
require '../functions/admin_template.php';

$currentPage = 'languages';
$template = new Template('Diller - Lahora Admin', $currentPage);

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
                                            <th>ID</th>
                                            <th>Dil</th>
                                            <th>Anahtar Kelime</th>
                                            <th>Değer</th>
                                            <th>İşlemler</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $database = new Database();
                                        $query = "SELECT * FROM translations";
                                        $results = $database->fetchAll($query);
                                        foreach ($results as $row) {
                                        ?>
                                        <tr>
                                            <td><?= $row['id']; ?></td>
                                            <td><?= $row['language']; ?></td>
                                            <td><?= $row['key']; ?></td>
                                            <td><?= $row['value']; ?></td>
                                            <td>
                                                <a class="cursor-pointer me-2 edit_lang"
                                                   data-id="<?= $row['id']; ?>"
                                                   data-key="<?= $row['key']; ?>"
                                                   data-value="<?= $row['value']; ?>"
                                                   data-language="<?= $row['language']; ?>"><i class="ti ti-pencil me-1"></i></a>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                    </div>
                </div>
                    <!-- Edit Lang Modal -->
                    <div class="modal fade" id="editLang" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-simple modal-edit-lang">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    <div class="text-center mb-6">
                                        <h4 class="mb-2">Yeni Kelime Ekle</h4>
                                    </div>
                                    <form id="editLangForm" class="row g-6" onsubmit="return false">
                                        <div class="col-12">
                                            <label class="form-label" for="modalEditLangKey">Anahtar Kelime</label>
                                            <input type="text" id="modalEditLangKey" name="modalEditLangKey" class="form-control" placeholder="welcome" />
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label" for="modalEditLangValue">Değer</label>
                                            <input type="text" id="modalEditLangValue" name="modalEditLangValue" class="form-control" placeholder="欢迎" />
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label" for="modalEditLanguage">Dil</label>
                                            <select id="modalEditLanguage" name="modalEditLanguage" class="select2 form-select">
                                                <option value="tr" selected>Türkçe</option>
                                                <option value="en">English</option>
                                            </select>
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

        // Open modal for editing language
        $(".edit_lang").on('click', function () {
            const key = $(this).data('key');
            const value = $(this).data('value');
            const language = $(this).data('language');
            const id = $(this).data('id');

            $("#modalEditLangKey").val(key);
            $("#modalEditLangValue").val(value);
            $("#modalEditLanguage").val(language).trigger("change"); // Set the selected language
            $("#editLangForm").data("action", "update").data("id", id); // Set action to update and store ID
            $('#editLang').modal('show');
        });

        // Handle form submission
        $("#editLangForm").on('submit', function (e) {
            e.preventDefault(); // Default form submission is prevented
            const action = $(this).data("action");
            let formData = {
                key: $("#modalEditLangKey").val(),
                value: $("#modalEditLangValue").val(),
                language: $("#modalEditLanguage").val(),
                action: action, // Specify whether insert or update
                id: action === "update" ? $(this).data("id") : null // Include ID if updating
            };

            $.ajax({
                url: '../functions/language/process_lang.php', // PHP file to handle the request
                type: 'POST',
                data: formData,
                success: function (response) {
                    $('#editLang').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: response,
                        showConfirmButton: false,
                        timer: 2000 // Auto close after 2 seconds
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