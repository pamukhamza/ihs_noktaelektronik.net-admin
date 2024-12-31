<?php
include_once '../functions/db.php';
require '../functions/admin_template.php';
 
$currentPage = 'users-detail';
$template = new Template('Kullanıcılar - NEBSİS Admin', $currentPage);
$database = new Database();
// head'i çağırıyoruz
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
// Fetch user's permissions
$query11 = "SELECT permission_id FROM user_permissions WHERE user_id = $user_id";
$userPermissions = $database->fetchAll($query11);
// Extract permission IDs for easier checking
$activePermissions = array_column($userPermissions, 'permission_id');
echo $userPermissions;
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
                                        function renderPermissions($permissions, $parentId = 0, $activePermissions = []) {
                                            if (isset($permissions[$parentId])) {
                                                echo '<ul class="list-group list-group-flush">';
                                                foreach ($permissions[$parentId] as $permission) {
                                                    $hasChildren = isset($permissions[$permission['id']]);
                                                    $toggleClass = $hasChildren ? 'toggle-permission' : '';
                                                    $plusIcon = $hasChildren ? '<span class="btn btn-sm btn-success plus-icon ms-5">+</span>' : '';
                                        
                                                    $isChecked = in_array($permission['id'], $activePermissions) ? 'checked' : '';
                                        
                                                    echo '<li class="list-group-item">';
                                                    echo '<div class="form-check ' . $toggleClass . '">';
                                                    echo '<input class="form-check-input" type="checkbox" id="permission_' . $permission['id'] . '" ' . $isChecked . '>';
                                                    echo '<label class="form-check-label" for="permission_' . $permission['id'] . '">' . $permission['name'] . '</label>';
                                                    echo $plusIcon;
                                                    echo '</div>';
                                        
                                                    // Recursively render child permissions
                                                    renderPermissions($permissions, $permission['id'], $activePermissions);
                                                    echo '</li>';
                                                }
                                                echo '</ul>';
                                            }
                                        }
                                        renderPermissions($permissionTree);
                                    ?>
                                </ul>
                                <button type="submit" class="btn btn-primary">Güncelle</button>
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
    $(document).on('click', '.plus-icon', function() {
        var parentLi = $(this).closest('li');
        parentLi.find('ul').toggle(); // Toggle visibility of child permissions
        $(this).text($(this).text() === '+' ? '-' : '+'); // Toggle "+" and "-" icon
    });
});
</script>
</body>
</html>