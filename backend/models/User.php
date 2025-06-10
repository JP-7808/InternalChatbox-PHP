<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/UserInterface.php';

class User implements UserInterface {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($data) {
        $emailPattern = '/^[a-zA-Z0-9._%+-]+@einfratechsys\.(tech|com|team)$/';
        if (!preg_match($emailPattern, $data['email'])) {
            return ['success' => false, 'message' => 'Invalid email domain'];
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (name, designation, location, email, password, employeeId, profileImage, role) 
                VALUES (:name, :designation, :location, :email, :password, :employeeId, :profileImage, :role)";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([
                ':name' => $data['name'],
                ':designation' => $data['designation'],
                ':location' => $data['location'],
                ':email' => $data['email'],
                ':password' => $hashedPassword,
                ':employeeId' => $data['employeeId'],
                ':profileImage' => $data['profileImage'] ?? 'default.jpg',
                ':role' => $data['role'] ?? 'employee'
            ]);
            return ['success' => true, 'message' => 'User registered successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return ['success' => true, 'user' => $user];
        }
        return ['success' => false, 'message' => 'Invalid credentials'];
    }

    public function getProfile($userId) {
        $sql = "SELECT id, name, designation, location, email, employeeId, profileImage, role, status 
                FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($userId, $data) {
        $sql = "UPDATE users SET name = :name, designation = :designation, location = :location, 
                profileImage = :profileImage WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([
                ':name' => $data['name'],
                ':designation' => $data['designation'],
                ':location' => $data['location'],
                ':profileImage' => $data['profileImage'] ?? 'default.jpg',
                ':id' => $userId
            ]);
            return ['success' => true, 'message' => 'Profile updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Update failed: ' . $e->getMessage()];
        }
    }

    public function updateEmployeeId($userId, $employeeId) {
        $sql = "UPDATE users SET employeeId = :employeeId WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([':employeeId' => $employeeId, ':id' => $userId]);
            return ['success' => true, 'message' => 'Employee ID updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Update failed: ' . $e->getMessage()];
        }
    }

    public function updateStatus($userId, $status) {
        $sql = "UPDATE users SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([':status' => $status, ':id' => $userId]);
            return ['success' => true, 'message' => 'Status updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Update failed: ' . $e->getMessage()];
        }
    }
}
?>