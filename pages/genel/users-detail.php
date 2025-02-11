<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'users';
$template = new Template('Kullanıcılar - NEBSİS Admin', $currentPage);
$database = new Database();
$template->head();
$user_id = $_GET['id'];

if ($user_id === 'new') {
    $query = "INSERT INTO users (username, email) VALUES (:name, :email)";
    $params = [
        'name' => '',
        'email' => '',
    ];
    if ($database->insert($query, $params)) {
        $lastId = $database->lastInsertId();
        $user_id = $lastId;
        $query = "SELECT * FROM users WHERE id = :id";
        $user = $database->fetch($query, ['id' => $user_id]);
    }
} elseif (is_numeric($user_id)) {
    $query = "SELECT * FROM users WHERE id = :id";
    $user = $database->fetch($query, ['id' => $user_id]);
}

// Fetch all permissions and order by parent_id
$query = "SELECT * FROM permissions ORDER BY parent_id, id";
$permissions = $database->fetchAll($query);

// Function to build hierarchical permissions
function buildPermissionTree($permissions) {
    $tree = [];
    foreach ($permissions as $permission) {
        $tree[$permission['parent_id']][] = $permission;
    }
    return $tree;
}
// Create permission tree based on parent_id
$permissionTree = buildPermissionTree($permissions);
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
                <div class="row g-4">
                    <!-- User Details Card -->
                    <div class="col-md-6 col-lg-6 mb-4">
                        <div class="card">
                            <h5 class="card-header">Kullanıcı Bilgileri</h5>
                            <div class="card-body">
                                <form id="updateUserForm">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Kullanıcı Adı</label>
                                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">E-posta</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Ad Soyad</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="roles" class="form-label">Rol</label>
                                        <select class="form-control" id="roles" name="roles" required>
                                            <option value="1" <?php echo ($user['roles'] === 1) ? 'selected' : ''; ?>>Admin</option>
                                            <option value="2" <?php echo ($user['roles'] === 2) ? 'selected' : ''; ?>>Satış Temsilcisi</option>
                                            <option value="3" <?php echo ($user['roles'] === 3) ? 'selected' : ''; ?>>Tekniker</option>
                                            <option value="4" <?php echo ($user['roles'] === 4) ? 'selected' : ''; ?>>Muhasebe</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Yeni Parola</label>
                                        <input type="password" class="form-control" id="password" name="password">
                                        <small class="form-text text-muted">Parolayı değiştirmek istemiyorsanız boş bırakın.</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Güncelle</button>
                                </form>
                            </div>
                        </div>
                    </div>
                   <!-- User Permissions Card -->
                    <div class="col-md-6 col-lg-6 mb-4">
                        <div class="card">
                            <h5 class="card-header">Kullanıcı Yetkileri</h5>
                            <div class="card-body">
                                <ul class="list-group">
                                    <?php
                                        function renderPermissions($permissions, $parentId = 0) {
                                            $activePermissions = [];
                                            global $database;
                                            global $user_id;
                                            $query11 = "SELECT permission_id FROM user_permissions WHERE user_id = :user_id";
                                            $userPermissions = $database->fetchAll($query11, ['user_id' => $user_id]);

                                            foreach ($userPermissions as $userPermission) {
                                                $activePermissions[] = $userPermission['permission_id'];
                                            }
                                            if (isset($permissions[$parentId])) {
                                                echo '<ul class="list-group list-group-flush">';
                                                foreach ($permissions[$parentId] as $permission) {
                                                    $hasChildren = isset($permissions[$permission['id']]);
                                                    $toggleClass = $hasChildren ? 'toggle-permission' : '';
                                                    $plusIcon = $hasChildren ? '<span class="btn btn-sm btn-success plus-icon ms-5">+</span>' : '';
                                                    $isChecked = in_array($permission['id'], $activePermissions) ? 'checked' : '';
                                                    echo '<li class="list-group-item">';
                                                    echo '<div class="form-check ' . $toggleClass . '">';
                                                    echo '<input class="form-check-input" type="checkbox" id="permission_' . $permission['id'] . '" name="permissions[]" value="' . $permission['id'] . '" ' . $isChecked . '>';
                                                    echo '<label class="form-check-label" for="permission_' . $permission['id'] . '">' . $permission['name'] . '</label>';
                                                    echo $plusIcon;
                                                    echo '</div>';
                                                    // Recursive function to render sub-permissions
                                                    renderPermissions($permissions, $permission['id'], $activePermissions);
                                                    echo '</li>';
                                                }
                                                echo '</ul>';
                                            }
                                        }
                                        renderPermissions($permissionTree);
                                    ?>
                                </ul>
                                <button type="button" class="btn btn-primary" id="updatePermissionsBtn">Güncelle</button>
                            </div>
                        </div>
                    </div>
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
<!-- build:js assets/vendor/js/core.js -->
<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/libs/i18n/i18n.js"></script>
<script src="assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Main JS -->
<script src="assets/js/main.js"></script>
<script>
    $(document).ready(function() {
        // Form submission via AJAX
        $('#updateUserForm').on('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission

            var formData = $(this).serialize(); // Serialize form data

            $.ajax({
                url: 'functions/users/update_user_detail.php', 
                type: 'POST',
                data: formData, 
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarıyla Güncellendi!',
                        text: response,
                        timer: 3000,  // The alert will close after 3000 milliseconds (3 seconds)
                        timerProgressBar: true,  // Optional: Shows a progress bar while the timer is running
                        willClose: () => {
                            location.reload(); // Reload the page when the alert closes
                        }
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Bir hata oluştu, lütfen tekrar deneyin.',
                    });
                }
            });
        });

        // Toggle child permissions visibility
        $(document).on('click', '.plus-icon', function() {
            var parentLi = $(this).closest('li');
            parentLi.find('ul').toggle(); // Toggle visibility of child permissions
            $(this).text($(this).text() === '+' ? '-' : '+'); // Toggle "+" and "-" icon
        });
    });
</script>
<script>
    document.getElementById('updatePermissionsBtn').addEventListener('click', function() {
        // Get the selected permissions
        let selectedPermissions = [];
        document.querySelectorAll('input[name="permissions[]"]:checked').forEach(function(checkbox) {
            selectedPermissions.push(checkbox.value);
        });

        // Send the selected permissions to the server using AJAX
        let user_id = <?php echo $user_id; ?>; // Assume user_id is available in your PHP context
        
        // AJAX request to update permissions
        $.ajax({
            url: 'functions/users/update_user_permissions.php',
            method: 'POST',
            data: {
                user_id: user_id,
                permissions: selectedPermissions
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Başarıyla Güncellendi!',
                    text: response,
                    timer: 2000,  // Auto-close after 2 seconds
                    showConfirmButton: false
                }).then(() => {
                    location.reload(); // Reload the page to reflect changes
                });
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Bir hata oluştu',
                    text: 'Lütfen tekrar deneyin.',
                });
            }
        });
    });
</script>
</body>
</html>