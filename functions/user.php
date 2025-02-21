<?php
include_once 'db.php';

// Create a Database instance
$database = new Database();

class User {
    private $database;
    private $session_key = 'user_session';

    public function __construct($database) {
        $this->database = $database;
    }

    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    // Simple function to get user permissions
    private function getUserPermissions($userId) {
        $query = "SELECT permission_id as id FROM user_permissions WHERE user_id = :userId";
        return $this->database->fetchAll($query, ['userId' => $userId]);
    }

    public function login($emailOrUsername, $password) {
        $query = "SELECT * FROM users WHERE (email = :emailOrUsername OR username = :emailOrUsername) AND is_active = 1 LIMIT 1";
        $params = ['emailOrUsername' => $emailOrUsername];
        $results = $this->database->fetchAll($query, $params);

        if (!empty($results)) {
            $user = $results[0];

            if (password_verify($password, $user['password'])) {
                // Get user permissions
                $permissions = $this->getUserPermissions($user['id']);
                
                session_name("user_session");
                session_start();
                $_SESSION[$this->session_key] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'full_name' => $user['full_name'],
                    'roles' => $user['roles'],
                    'permissions' => array_column($permissions, 'id')
                ];
                session_regenerate_id(true);
                return true;
            }
        }
        return false;
    }

    public function isLoggedIn() {
        return isset($_SESSION[$this->session_key]);
    }

    // Simple permission check
    public function hasPermission($permissionId) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        return in_array($permissionId, $_SESSION[$this->session_key]['permissions']);
    }

    public function getUserPermissionsList() {
        if (!$this->isLoggedIn()) {
            return [];
        }
        return $_SESSION[$this->session_key]['permissions'];
    }

    public function logout() {
        session_name("user_session");
        session_start();
        unset($_SESSION[$this->session_key]);
        session_destroy();
        header("Location:https://www.noktaelektronik.net/admin/index");
    }
}
?>
