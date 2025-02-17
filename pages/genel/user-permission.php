<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'users';
$template = new Template('Yetkiler - NEBSİS Admin', $currentPage);
$database = new Database();

// Fetch all permissions
$query = "SELECT * FROM permissions WHERE parent_id = 0 ";
$permissions = $database->fetchAll($query);

$query = "SELECT * FROM permissions ORDER BY parent_id, name";
$permissions_select = $database->fetchAll($query);

// Create a function to generate the dropdown options recursively
function generatePermissionOptions($permissions_select, $parentId = 0, $level = 0) {
    $options = '';
    foreach ($permissions_select as $permission) {
        if ($permission['parent_id'] == $parentId) {
            $indentation = str_repeat('---', $level); // Indentation for nested items
            $options .= '<option value="' . $permission['id'] . '">' . $indentation . ' ' . $permission['name'] . '</option>';
            $options .= generatePermissionOptions($permissions_select, $permission['id'], $level + 1); // Recursive call for children
        }
    }
    return $options;
}

// Generate the options for the select field
$options = generatePermissionOptions($permissions_select);

// head'i çağırıyoruz
$template->head();
?>
<body>
<div class="layout-wrapper layout-content-navbar">
  <div class="layout-container">
    <?php $template->header(); ?>
      <!-- Content wrapper -->
      <div class="content-wrapper">
        <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">Yetkiler</h4>
            
            <!-- Permission Table -->
            <div class="card">
              <div class="card-header">
                <h5 class="card-title">Tüm Yetkiler</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
                  Yeni Yetki Ekle
                </button>
              </div>
              <div class="card-datatable table-responsive">
                <table class="datatables-permissions table border-top">
                  <thead>
                    <tr>
                      <th>İsim</th>
                      <th>Açıklama</th>
                      <th>Oluşturulma Tarihi</th>
                      <th>İşlemler</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($permissions as $permission): ?>
                    <tr>
                      <td>
                        <span class="permission-name" data-id="<?php echo $permission['id']; ?>" style="cursor: pointer; color: blue;">
                          <?php echo $permission['id'] . ' - ' . $permission['name']; ?>
                        </span>
                      </td>
                      <td><?php echo $permission['description']; ?></td>
                      <td><?php echo $permission['created_at']; ?></td>
                      <td>
                        <button type="button" class="btn btn-sm btn-icon btn-primary edit-permission" data-id="<?= $permission['id']; ?>" 
                                                                                                      data-name="<?= $permission['name']; ?>"
                                                                                                      data-description="<?= $permission['description']; ?>">
                          <i class="ti ti-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-danger delete-permission" data-id="<?php echo $permission['id']; ?>">
                          <i class="ti ti-trash"></i>
                        </button>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <!--/ Permission Table -->

            <!-- Add Permission Modal -->
            <div class="modal fade" id="addPermissionModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Yeni Yetki Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form id="addPermissionForm" onsubmit="return false">
                      <div class="mb-3">
                        <label for="permissionName" class="form-label">Yetki Adı</label>
                        <input type="text" class="form-control" id="permissionName" name="name" required>
                      </div>
                      <div class="mb-3">
                        <label for="permissionDescription" class="form-label">Açıklama</label>
                        <input type="text" class="form-control" id="permissionDescription" name="description">
                      </div>
                      <div class="mb-3">
                          <label for="parent_id" class="form-label">Üst Yetki</label>
                          <select class="form-control" id="parent_id" name="parent_id" required>
                              <option value="0">Seçiniz</option>
                              <?php echo $options; ?>
                          </select>
                      </div>
                      <button type="submit" class="btn btn-primary">Yetki Ekle</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <!--/ Add Permission Modal -->

            <!-- Edit Permission Modal -->
            <div class="modal fade" id="editPermissionModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Yetki Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form id="editPermissionForm" onsubmit="return false">
                      <input type="hidden" id="editPermissionId" name="id">
                      <div class="mb-3">
                        <label for="editPermissionName" class="form-label">Yetki Adı</label>
                        <input type="text" class="form-control" id="editPermissionName" name="name" required>
                      </div>
                      <div class="mb-3">
                        <label for="editPermissionDescription" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="editPermissionDescription" name="description" rows="3"></textarea>
                      </div>
                      <button type="submit" class="btn btn-primary">Güncelle</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <!--/ Edit Permission Modal -->

          </div>
          <!-- / Content -->
   
          <?php $template->footer(); ?>
      </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
  </div>
  <script src="assets/vendor/libs/jquery/jquery.js"></script>
  <script src="assets/vendor/libs/popper/popper.js"></script>
  <script src="assets/vendor/js/bootstrap.js"></script>
  <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="assets/vendor/libs/hammer/hammer.js"></script>
  <script src="assets/vendor/libs/i18n/i18n.js"></script>
  <script src="assets/vendor/libs/typeahead-js/typeahead.js"></script>
  <script src="assets/vendor/js/menu.js"></script>
  <script src="assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
  <script src="assets/vendor/libs/@form-validation/umd/bundle/popular.min.js"></script>
  <script src="assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js"></script>
  <script src="assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js"></script>
  <!-- Main JS -->
  <script src="assets/js/main.js"></script>
  <!-- Page JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
$(document).ready(function () {
    var dt_permission_table = $('.datatables-permissions');

    // Alt Yetkileri Getir ve Satır Altına Ekle
    $(document).on('click', '.permission-name', function () {
        var parentId = $(this).data('id');
        var currentRow = $(this).closest('tr');

        // Eğer alt yetki satırı zaten açıksa kapatma işlemi yapılmayacak
        if (currentRow.next().hasClass('sub-permission-row') && currentRow.next().data('parent-id') === parentId) {
            currentRow.next().remove();
            return;
        }

        $.ajax({
            url: 'functions/permission/sub_permissions.php',
            type: 'POST',
            data: { parent_id: parentId },
            success: function (response) {
                console.log(response);
                var subPermissions = JSON.parse(response);
                var html = '<tr class="sub-permission-row" data-parent-id="' + parentId + '"><td colspan="5"><table class="table table-sm mb-0"><tbody>';

                if (subPermissions.length > 0) {
                    subPermissions.forEach(function (permission) {
                        html += `
                            <tr>
                                <td>
                                  <span class="permission-name" data-id="${permission.id}" style="cursor: pointer; color: blue;">${permission.id}  -  ${permission.name}
                                  </span>
                                </td>
                                <td>${permission.description}</td>
                                <td>${permission.created_at}</td>
                                <td>
                                  <button type="button" class="btn btn-sm btn-icon btn-primary edit-permission" data-id="${permission.id}" data-name="${permission.name}" data-description="${permission.description}">
                                    <i class="ti ti-edit"></i>
                                  </button>
                                  <button type="button" class="btn btn-sm btn-icon btn-danger delete-permission" data-id="${permission.id}">
                                    <i class="ti ti-trash"></i>
                                  </button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html += '<tr><td colspan="4">Alt yetki bulunamadı.</td></tr>';
                }
                html += '</tbody></table></td></tr>';
                // Yeni satırı mevcut satırın altına ekle
                currentRow.after(html);
            },
            error: function () {
                alert('Alt yetkiler yüklenirken bir hata oluştu.');
            }
        });
    });

    // editModal'ı aç ve verileri doldur
    $(document).on('click', '.edit-permission', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var description = $(this).data('description');

        // Modal içindeki alanlara değerleri ata
        $('#editPermissionId').val(id);
        $('#editPermissionName').val(name);
        $('#editPermissionDescription').val(description);

        // Modal'ı göster
        $('#editPermissionModal').modal('show');
    });

    // editModal Form gönderme işlemi
    $('#editPermissionForm').on('submit', function () {
        var formData = $(this).serialize(); // Form verilerini al

        $.ajax({
            url: 'functions/permission/update_permission.php',
            type: 'POST',
            data: formData,
            success: function (response) {
                var result = JSON.parse(response);
                if (result.success) {
                    // Başarılı mesajı ve tabloyu güncelle
                    alert('Yetki başarıyla güncellendi!');
                    location.reload(); // Sayfayı yeniden yükle
                } else {
                    alert('Yetki güncellenirken bir hata oluştu: ' + result.message);
                }
            },
            error: function () {
                alert('Bir hata oluştu. Lütfen daha sonra tekrar deneyin.');
            }
        });

        return false; // Formun sayfayı yenilemesini engelle
    });

    //DELETE
    $(document).on('click', '.delete-permission', function (e) {
        e.preventDefault();
        var per_id = $(this).data('id'); 
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
                    url: 'functions/functions.php', 
                    type: 'POST',
                    data: { id: per_id, tablename: 'permissions', type: 'delete' },
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

    //ADD 
    $('#addPermissionForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the form from submitting normally

        var formData = $(this).serialize(); // Serialize form data

        $.ajax({
            url: 'functions/permission/add_permission.php', // The PHP file that will handle the request
            type: 'POST',
            data: formData, // Send the serialized form data
            success: function(response) {
              $('#addPermissionModal').modal('hide');
                // Assuming the response is a success message
                Swal.fire({
                    title: 'Başarılı!',
                    text: 'Yeni yetki başarıyla eklendi.',
                    icon: 'success',
                    timer: 2000, // 3000 milliseconds = 3 seconds
                    timerProgressBar: true, // Optionally show a progress bar
                    showConfirmButton: false, // Hide the confirm button
                }).then(() => {
                    location.reload(); // Reload the page after the timer ends
                });

            },
            error: function() {
              $('#addPermissionModal').modal('hide');
                Swal.fire(
                    'Hata!',
                    'Yetki eklenirken bir hata oluştu.',
                    'error'
                );
            }
        });
    });

});
</script>
</body>
</html>