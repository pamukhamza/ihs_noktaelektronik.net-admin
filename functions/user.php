<?php
include_once '../../functions/db.php';

// Create a Database instance
$database = new Database();

class User {
    private $database;
    private $session_key = 'user_session';

    // Constructor to inject the database dependency
    public function __construct($database) {
        $this->database = $database;
    }

    // Hashing passwords securely using password_hash
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    // Function to verify credentials and start session
    public function login($emailOrUsername, $password) {
        $query = "SELECT * FROM users WHERE email = :emailOrUsername OR username = :emailOrUsername LIMIT 1";
        $params = ['emailOrUsername' => $emailOrUsername];
        $results = $this->database->fetchAll($query, $params);

        // Fetch the user from the query result
        if (!empty($results)) {
            $user = $results[0]; // Assuming the first result is the user we want

            // Verify password and start session if correct
            if (password_verify($password, $user['password'])) {
                session_name("user_session");
                session_start();
                $_SESSION[$this->session_key] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email']
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

    // Log the user out
    public function logout() {
        unset($_SESSION[$this->session_key]);
        session_destroy();
        header("Location:/");
    }
}
?>
