<?php
include_once 'db.php';

// Create a Database instance
$database = new Database();

class User {
    private $database;
    private $session_key = 'user_session';

    public function __construct($database) {
        $this->database = $database;
        
        // Set session parameters
        ini_set('session.gc_maxlifetime', $this->session_lifetime);
        ini_set('session.cookie_lifetime', $this->session_lifetime);
        
        // Set session cookie parameters
        session_set_cookie_params([
            'lifetime' => $this->session_lifetime,
            'path' => '/',
            'secure' => true,    // Only transmit over HTTPS
            'httponly' => true,  // Prevent JavaScript access
            'samesite' => 'Strict' // CSRF protection
        ]);
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
                
                // Start session with custom name
                session_name("user_session");
                session_start();
                
                // Set last activity time
                $_SESSION['last_activity'] = time();
                
                // Store user data in session
                $_SESSION[$this->session_key] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'full_name' => $user['full_name'],
                    'roles' => $user['roles'],
                    'permissions' => array_column($permissions, 'id')
                ];
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                return true;
            }
        }
        return false;
    }

    public function isLoggedIn() {
        session_name("user_session");
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_session'])) {
            header("Location: https://www.noktaelektronik.net/admin/index");
            exit();
            
        }

        // Check if session has expired
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > $this->session_lifetime)) {
            // Session has expired, destroy it
            $this->logout();
            return false;
        }

        // Update last activity time
        $_SESSION['last_activity'] = time();
        return true;
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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy the session
        session_destroy();
        
        header("Location:https://www.noktaelektronik.net/admin/index");
        exit();
    }
}
?>
