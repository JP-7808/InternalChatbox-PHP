<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

class ProfileController {
    private $user;

    public function __construct($pdo) {
        $this->user = new User($pdo);
    }

    public function getProfile() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $profile = $this->user->getProfile($_SESSION['user_id']);
        echo json_encode(['success' => true, 'data' => $profile]);
    }

    public function updateProfile() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $data = [
            'name' => $_POST['name'],
            'designation' => $_POST['designation'],
            'location' => $_POST['location'],
            'profileImage' => $_FILES['profileImage']['name'] ?? 'default.jpg'
        ];

        if (isset($_FILES['profileImage']) && $_FILES['profileImage']['size'] <= 1 * 1024 * 1024) {
            move_uploaded_file($_FILES['profileImage']['tmp_name'], __DIR__ . '/../../frontend/assets/images/' . $data['profileImage']);
        }

        $result = $this->user->updateProfile($_SESSION['user_id'], $data);
        echo json_encode($result);
    }

    public function updateEmployeeId() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $result = $this->user->updateEmployeeId($_SESSION['user_id'], $_POST['employeeId']);
        echo json_encode($result);
    }

    public function updateStatus() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $result = $this->user->updateStatus($_SESSION['user_id'], $_POST['status']);
        echo json_encode($result);
    }
}
?>