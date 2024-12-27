<?php
include_once '../functions/db.php';
require '../functions/admin_template.php';

$currentPage = 'user-permissions';
$template = new Template('Yetkiler - NEBSİS Admin', $currentPage);
$database = new Database();

// Fetch all permissions
$query = "SELECT p.id, p.name, p.description, p.created_at, 
          (SELECT COUNT(*) FROM user_permissions WHERE permission_id = p.id) as user_count,
          (SELECT COUNT(*) FROM role_permissions WHERE permission_id = p.id) as role_count
          FROM permissions p";
$permissions = $database->fetchAll($query);

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
                      <th>ID</th>
                      <th>İsim</th>
                      <th>Açıklama</th>
                      <th>Atanan Kullanıcılar</th>
                      <th>Atanan Roller</th>
                      <th>Oluşturulma Tarihi</th>
                      <th>İşlemler</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($permissions as $permission): ?>
                    <tr>
                      <td><?php echo $permission['id']; ?></td>
                      <td><?php echo htmlspecialchars($permission['name']); ?></td>
                      <td><?php echo htmlspecialchars($permission['description']); ?></td>
                      <td><?php echo $permission['user_count']; ?></td>
                      <td><?php echo $permission['role_count']; ?></td>
                      <td><?php echo $permission['created_at']; ?></td>
                      <td>
                        <button type="button" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="modal" data-bs-target="#editPermissionModal" data-id="<?php echo $permission['id']; ?>">
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
                    <form id="addPermissionForm" method="post" onsubmit="return false">
                      <div class="mb-3">
                        <label for="permissionName" class="form-label">Yetki Adı</label>
                        <input type="text" class="form-control" id="permissionName" name="name" required>
                      </div>
                      <div class="mb-3">
                        <label for="permissionDescription" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="permissionDescription" name="description" rows="3"></textarea>
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
  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../assets/vendor/libs/hammer/hammer.js"></script>
  <script src="../assets/vendor/libs/i18n/i18n.js"></script>
  <script src="../assets/vendor/libs/typeahead-js/typeahead.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>
  <script src="../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
  <script src="../assets/vendor/libs/@form-validation/umd/bundle/popular.min.js"></script>
  <script src="../assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js"></script>
  <script src="../assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js"></script>
  <!-- Main JS -->
  <script src="../assets/js/main.js"></script>
  <!-- Page JS -->
 

<script>
$(document).ready(function() {
    var dt_permission_table = $('.datatables-permissions');

    // Permission Table
    if (dt_permission_table.length) {
      dt_permission_table.DataTable({
        order: [[1, 'asc']],
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json'
        }
      });
    }
  
    // Add New Permission
    var addPermissionForm = document.getElementById('addPermissionForm');
    var fv = FormValidation.formValidation(addPermissionForm, {
      fields: {
        name: {
          validators: {
            notEmpty: {
              message: 'Lütfen yetki adını giriniz'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: '.mb-3'
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        autoFocus: new FormValidation.plugins.AutoFocus()
      }
    }).on('core.form.valid', function() {
      var formData = new FormData(addPermissionForm);
      $.ajax({
        url: '../functions/permission/add_permission.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          response = JSON.parse(response); // Parse the JSON response
          if (response.success) {
            $('#addPermissionModal').modal('hide');
            location.reload(); // Reload the page to refresh the table
          } else {
            alert('Yetki eklenirken bir hata oluştu: ' + response.message);
          }
        },
        error: function() {
          alert('Bir hata oluştu. Lütfen tekrar deneyin.');
        }
      });
    });

    // Edit Permission
    var editPermissionForm = document.getElementById('editPermissionForm');
    var editPermissionValidator = FormValidation.formValidation(editPermissionForm, {
      fields: {
        name: {
          validators: {
            notEmpty: {
              message: 'Lütfen yetki adını giriniz'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: '.mb-3'
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        autoFocus: new FormValidation.plugins.AutoFocus()
      }
    }).on('core.form.valid', function() {
      var formData = new FormData(editPermissionForm);
      $.ajax({
        url: '../functions/permission/update_permission.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          response = JSON.parse(response); // Parse the JSON response
          if (response.success) {
            $('#editPermissionModal').modal('hide');
            location.reload(); // Reload the page to refresh the table
          } else {
            alert('Yetki güncellenirken bir hata oluştu: ' + response.message);
          }
        },
        error: function() {
          alert('Bir hata oluştu. Lütfen tekrar deneyin.');
        }
      });
    });

    // Edit Permission Click Handler
    $(document).on('click', '[data-bs-target="#editPermissionModal"]', function () {
      var id = $(this).data('id');
      $.ajax({
        url: '../functions/permission/get_permission.php',
        type: 'GET',
        data: { id: id },
        success: function(response) {
          response = JSON.parse(response); // Parse the JSON response
          if (response.success) {
            $('#editPermissionId').val(response.permission.id);
            $('#editPermissionName').val(response.permission.name);
            $('#editPermissionDescription').val(response.permission.description);
          } else {
            alert('Yetki bilgileri alınırken bir hata oluştu: ' + response.message);
          }
        },
        error: function() {
          alert('Bir hata oluştu. Lütfen tekrar deneyin.');
        }
      });
    });

    // Delete Permission
    $(document).on('click', '.delete-permission', function () {
      var id = $(this).data('id');
      if (confirm('Bu yetkiyi silmek istediğinizden emin misiniz?')) {
        $.ajax({
          url: '../functions/permission/delete_permission.php',
          type: 'POST',
          data: { id: id },
          success: function(response) {
            response = JSON.parse(response); // Parse the JSON response
            if (response.success) {
              location.reload(); // Reload the page to refresh the table
            } else {
              alert('Yetki silinirken bir hata oluştu: ' + response.message);
            }
          },
          error: function() {
            alert('Bir hata oluştu. Lütfen tekrar deneyin.');
          }
        });
      }
    });
});
</script>
</body>
</html>