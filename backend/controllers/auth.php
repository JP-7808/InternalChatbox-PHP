<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $user;

    public function __construct($pdo) {
        $this->user = new User($pdo);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'designation' => $_POST['designation'],
                'location' => $_POST['location'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'employeeId' => $_POST['employeeId'],
                'profileImage' => $_FILES['profileImage']['name'] ?? 'default.jpg',
                'role' => $_POST['role'] ?? 'employee'
            ];

            if (isset($_FILES['profileImage']) && $_FILES['profileImage']['size'] <= 1 * 1024 * 1024) {
                move_uploaded_file($_FILES['profileImage']['tmp_name'], __DIR__ . '/../../frontend/assets/images/' . $data['profileImage']);
            }

            $result = $this->user->register($data);
            echo json_encode($result);
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->user->login($_POST['email'], $_POST['password']);
            if ($result['success']) {
                session_start();
                $_SESSION['user_id'] = $result['user']['id'];
                $_SESSION['role'] = $result['user']['role'];
            }
            echo json_encode($result);
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    }
}
?>