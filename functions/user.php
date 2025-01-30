<?php
include_once 'db.php';

// Create a Database instance
$database = new Database();

class User {
    private $database;
    private $session_key = 'user_session';
    private $permissions_cache = [];

    // Constructor to inject the database dependency
    public function __construct($database) {
        $this->database = $database;
    }

    // Hashing passwords securely using password_hash
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    // Get user permissions from database including parent permissions
    private function getUserPermissions($userId) {
        // First, get direct permissions
        $query = "WITH RECURSIVE permission_hierarchy AS (
            -- Get direct permissions first
            SELECT p.id 
            FROM permissions p
            INNER JOIN user_permissions up ON p.id = up.permission_id
            WHERE up.user_id = :userId

            UNION ALL

            -- Get parent permissions
            SELECT parent.id
            FROM permissions parent
            INNER JOIN permission_hierarchy ph ON parent.id = ph.parent_id
        )
        SELECT DISTINCT id FROM permission_hierarchy";
        
        return $this->database->fetchAll($query, ['userId' => $userId]);
    }

    // Function to verify credentials and start session
    public function login($emailOrUsername, $password) {
        $query = "SELECT * FROM users WHERE (email = :emailOrUsername OR username = :emailOrUsername) AND is_active = 1 LIMIT 1";
        $params = ['emailOrUsername' => $emailOrUsername];
        $results = $this->database->fetchAll($query, $params);

        // Fetch the user from the query result
        if (!empty($results)) {
            $user = $results[0];

            // Verify password and start session if correct
            if (password_verify($password, $user['password'])) {
                // Get user permissions including inherited ones
                $permissions = $this->getUserPermissions($user['id']);
                
                session_name("user_session");
                session_start();
                $_SESSION[$this->session_key] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'permissions' => array_column($permissions, 'id')
                ];
                session_regenerate_id(true);
                return true;
            }
        }

        return false;
    }

    // Check if a user is logged in
    public function isLoggedIn() {
        return isset($_SESSION[$this->session_key]);
    }

    // Get all child permissions for a given permission
    private function getChildPermissions($permissionId) {
        if (isset($this->permissions_cache[$permissionId])) {
            return $this->permissions_cache[$permissionId];
        }

        $query = "WITH RECURSIVE permission_tree AS (
            -- Base case: get the permission itself
            SELECT id
            FROM permissions
            WHERE id = :permissionId

            UNION ALL

            -- Recursive case: get all children
            SELECT p.id
            FROM permissions p
            INNER JOIN permission_tree pt ON p.parent_id = pt.id
        )
        SELECT DISTINCT id FROM permission_tree";

        $children = $this->database->fetchAll($query, ['permissionId' => $permissionId]);
        $childIds = array_column($children, 'id');
        $this->permissions_cache[$permissionId] = $childIds;
        
        return $childIds;
    }

    // Check if user has specific permission (including inherited permissions)
    public function hasPermission($permissionId) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $userPermissions = $_SESSION[$this->session_key]['permissions'];
        
        // Check direct permissions first
        if (in_array($permissionId, $userPermissions)) {
            return true;
        }

        // Check parent permissions
        foreach ($userPermissions as $userPermId) {
            $childPermissions = $this->getChildPermissions($userPermId);
            if (in_array($permissionId, $childPermissions)) {
                return true;
            }
        }
        
        return false;
    }

    // Get all permissions for current user
    public function getUserPermissionsList() {
        if (!$this->isLoggedIn()) {
            return [];
        }
        return $_SESSION[$this->session_key]['permissions'];
    }

    // Log the user out
    public function logout() {
        session_name("user_session");
        session_start();

        unset($_SESSION[$this->session_key]);
        session_destroy();
        header("Location:https://www.noktaelektronik.net/admin/index");
    }
}
?>
