<?php
include_once '../db.php';

$database = new Database();
$username = $_POST['username'];
$email = $_POST['email'];
$full_name = $_POST['full_name'];
$roles = $_POST['roles'];
$password = $_POST['password'];
$user_id = $_POST['user_id'];

// Prepare the base update query
$query = "UPDATE users SET username = :username, email = :email, full_name = :full_name, roles = :roles";

// Prepare parameters for the query
$params = [
    'username' => $username,
    'email' => $email,
    'full_name' => $full_name,
    'roles' => $roles,
    'user_id' => $user_id
];

// If password is provided, hash it and include it in the query
if (!empty($password)) {
    $passwordHash = password_hash($password, PASSWORD_DEFAULT); // Hash the password
    $query .= ", password = :password";  // Add password update to the query
    $params['password'] = $passwordHash; // Add hashed password to the params
}

// Finalize the query with the user_id condition
$query .= " WHERE id = :user_id";

// Execute the query
if ($database->update($query, $params)) {
    echo "Kullanıcı bilgileri başarıyla güncellendi.";
} else {
    echo "Bir hata oluştu, lütfen tekrar deneyin.";
}
?>
