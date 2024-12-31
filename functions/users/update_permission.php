<?php
include_once '../db.php';

$database = new Database();
$user_id = $_POST['user_id'];
$permission_id = $_POST['permission_id'];
$is_active = $_POST['is_active'];

if ($is_active) {
    // Add permission
    $query = "INSERT INTO user_permissions (user_id, permission_id) VALUES (:user_id, :permission_id)";
    $params = ['user_id' => $user_id, 'permission_id' => $permission_id];
} else {
    // Remove permission
    $query = "DELETE FROM user_permissions WHERE user_id = :user_id AND permission_id = :permission_id";
    $params = ['user_id' => $user_id, 'permission_id' => $permission_id];
}

if ($database->execute($query, $params)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
