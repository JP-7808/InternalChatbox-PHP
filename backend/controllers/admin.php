<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ChatMessage.php';
require_once __DIR__ . '/../models/ChatGroup.php';

class AdminController {
    private $user;
    private $chatMessage;
    private $chatGroup;
    private $pdo;

    public function __construct($pdo) {
        $this->user = new User($pdo);
        $this->chatMessage = new ChatMessage($pdo);
        $this->chatGroup = new ChatGroup($pdo);
        $this->pdo = $pdo;
    }

    public function dashboard() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $sql = "SELECT id, name, email, role, status FROM users";
        $stmt = $this->pdo->query($sql);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $users]);
    }

    public function editUser() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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
        $result = $this->user->updateProfile($_POST['user_id'], $data);
        echo json_encode($result);
    }

    public function deleteUser() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $sql = "UPDATE users SET status = 'offline' WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([':id' => $_POST['user_id']]);
            echo json_encode(['success' => true, 'message' => 'User deactivated successfully']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Deactivation failed: ' . $e->getMessage()]);
        }
    }

    public function changePassword() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $hashedPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([':password' => $hashedPassword, ':id' => $_POST['user_id']]);
            echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Password change failed: ' . $e->getMessage()]);
        }
    }

    public function viewGroupChat() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $messages = $this->chatMessage->getMessages(null, null, $_POST['group_id']);
        echo json_encode(['success' => true, 'data' => $messages]);
    }

    public function downloadGroupChat() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $messages = $this->chatMessage->getMessages(null, null, $_POST['group_id']);
        $content = "Group Chat History\n\n";
        foreach ($messages as $msg) {
            $content .= "[{$msg['sent_at']}] {$msg['sender_name']}: {$msg['message_text']}\n";
        }
        $file = 'group_chat_' . $_POST['group_id'] . '.txt';
        file_put_contents(__DIR__ . '/../../frontend/assets/downloads/' . $file, $content);
        echo json_encode(['success' => true, 'file' => $file]);
    }

    public function viewPrivateChat() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $messages = $this->chatMessage->getMessages($_POST['user_id1'], $_POST['user_id2'], null);
        echo json_encode(['success' => true, 'data' => $messages]);
    }

    public function downloadPrivateChat() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $messages = $this->chatMessage->getMessages($_POST['user_id1'], $_POST['user_id2'], null);
        $content = "Private Chat History\n\n";
        foreach ($messages as $msg) {
            $content .= "[{$msg['sent_at']}] {$msg['sender_name']}: {$msg['message_text']}\n";
        }
        $file = 'private_chat_' . $_POST['user_id1'] . '_' . $_POST['user_id2'] . '.txt';
        file_put_contents(__DIR__ . '/../../frontend/assets/downloads/' . $file, $content);
        echo json_encode(['success' => true, 'file' => $file]);
    }

    public function toggleGroupAdmin() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $result = $this->chatGroup->toggleGroupAdmin($_POST['group_id'], $_POST['user_id'], $_POST['is_admin']);
        echo json_encode($result);
    }
}
?>