<?php
include_once '../db.php'; // Adjust the path based on your directory structure

$database = new Database();
$user_id = $_POST['user_id'];
$permissions = $_POST['permissions']; // Array of selected permission IDs

// Remove existing permissions for the user
$deleteQuery = "DELETE FROM user_permissions WHERE user_id = :user_id";
$database->update($deleteQuery, ['user_id' => $user_id]);

// Insert the new permissions
foreach ($permissions as $permission_id) {
    $insertQuery = "INSERT INTO user_permissions (user_id, permission_id) VALUES (:user_id, :permission_id)";
    $params = [
        'user_id' => $user_id,
        'permission_id' => $permission_id
    ];
    $database->update($insertQuery, $params);
}

echo "Kullanıcı izinleri başarıyla güncellendi!";
?>
