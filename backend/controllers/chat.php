<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/ChatMessage.php';
require_once __DIR__ . '/../models/ChatGroup.php';
require_once __DIR__ . '/../models/GroupMember.php';

class ChatController {
    private $chatMessage;
    private $chatGroup;
    private $groupMember;
    private $pdo;

    public function __construct($pdo) {
        $this->chatMessage = new ChatMessage($pdo);
        $this->chatGroup = new ChatGroup($pdo);
        $this->groupMember = new GroupMember($pdo);
        $this->pdo = $pdo;
    }

    public function sendMessage() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $receiverId = $_POST['receiver_id'] ?? null;
        $groupId = $_POST['group_id'] ?? null;
        $messageText = $_POST['message_text'];

        $result = $this->chatMessage->sendMessage($_SESSION['user_id'], $receiverId, $groupId, $messageText);
        if ($result['success']) {
            $result['message_id'] = $this->pdo->lastInsertId();
        }
        echo json_encode($result);
    }

    public function uploadFile() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if ($_FILES['file']['size'] > 1 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File size exceeds 1MB']);
            return;
        }

        $messageResult = $this->chatMessage->sendMessage(
            $_SESSION['user_id'],
            $_POST['receiver_id'] ?? null,
            $_POST['group_id'] ?? null,
            'File uploaded'
        );

        if ($messageResult['success']) {
            $messageId = $this->pdo->lastInsertId();
            $filePath = 'uploads/' . $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], __DIR__ . '/../../frontend/assets/uploads/' . $_FILES['file']['name']);
            $sql = "INSERT INTO files (message_id, file_path, file_size) VALUES (:message_id, :file_path, :file_size)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':message_id' => $messageId,
                ':file_path' => $filePath,
                ':file_size' => $_FILES['file']['size']
            ]);
            echo json_encode(['success' => true, 'message' => 'File uploaded successfully']);
        } else {
            echo json_encode($messageResult);
        }
    }

    public function createGroup() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $result = $this->chatGroup->createGroup($_POST['group_name'], $_SESSION['user_id']);
        echo json_encode($result);
    }

    public function updateGroup() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $result = $this->chatGroup->updateGroup($_POST['group_id'], $_POST['group_name']);
        echo json_encode($result);
    }

    public function deleteGroup() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $result = $this->chatGroup->deleteGroup($_POST['group_id']);
        echo json_encode($result);
    }

    public function addGroupMember() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $result = $this->chatGroup->addGroupMember($_POST['group_id'], $_POST['user_id'], $_POST['is_admin'] ?? false);
        echo json_encode($result);
    }

    public function removeGroupMember() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $result = $this->chatGroup->removeGroupMember($_POST['group_id'], $_POST['user_id']);
        echo json_encode($result);
    }

    public function getMessages() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $messages = $this->chatMessage->getMessages($_SESSION['user_id'], $_POST['receiver_id'] ?? null, $_POST['group_id'] ?? null);
        echo json_encode(['success' => true, 'data' => $messages]);
    }

    public function editMessage($messageId, $messageText) {
        // Check if user is sender or group admin
        $sql = "SELECT sender_id, group_id FROM chat_messages WHERE id = :id AND is_deleted = FALSE";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $messageId]);
        $message = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$message) {
            return ['success' => false, 'message' => 'Message not found'];
        }

        session_start();
        $userId = $_SESSION['user_id'];
        $isGroupAdmin = false;
        if ($message['group_id']) {
            $sql = "SELECT is_admin FROM group_members WHERE group_id = :group_id AND user_id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':group_id' => $message['group_id'], ':user_id' => $userId]);
            $isGroupAdmin = $stmt->fetchColumn();
        }

        if ($message['sender_id'] != $userId && !$isGroupAdmin) {
            return ['success' => false, 'message' => 'Unauthorized to edit message'];
        }

        $sql = "UPDATE chat_messages SET message_text = :message_text WHERE id = :id AND is_deleted = FALSE";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([':message_text' => $messageText, ':id' => $messageId]);
            return ['success' => true, 'message' => 'Message updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Update failed: ' . $e->getMessage()];
        }
    }

    public function deleteMessage($messageId) {
        // Check if user is sender or group admin
        $sql = "SELECT sender_id, group_id FROM chat_messages WHERE id = :id AND is_deleted = FALSE";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $messageId]);
        $message = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$message) {
            return ['success' => false, 'message' => 'Message not found'];
        }

        session_start();
        $userId = $_SESSION['user_id'];
        $isGroupAdmin = false;
        if ($message['group_id']) {
            $sql = "SELECT is_admin FROM group_members WHERE group_id = :group_id AND user_id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':group_id' => $message['group_id'], ':user_id' => $userId]);
            $isGroupAdmin = $stmt->fetchColumn();
        }

        if ($message['sender_id'] != $userId && !$isGroupAdmin) {
            return ['success' => false, 'message' => 'Unauthorized to delete message'];
        }

        $sql = "UPDATE chat_messages SET is_deleted = TRUE WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([':id' => $messageId]);
            return ['success' => true, 'message' => 'Message deleted successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Deletion failed: ' . $e->getMessage()];
        }
    }

    public function getUsers() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $sql = "SELECT id, name, status, profileImage FROM users WHERE id != :user_id AND status != 'offline'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $users]);
    }

    public function getGroups() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $sql = "SELECT cg.id, cg.group_name, cg.group_image 
                FROM chat_groups cg 
                JOIN group_members gm ON cg.id = gm.group_id 
                WHERE gm.user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $groups]);
    }

    public function getGroupMembers() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $groupId = $_GET['group_id'] ?? null;
        if (!$groupId) {
            echo json_encode(['success' => false, 'message' => 'Group ID required']);
            return;
        }
        $members = $this->groupMember->getGroupMembers($groupId);
        echo json_encode(['success' => true, 'data' => $members]);
    }

    public function checkGroupAdmin() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $groupId = $_GET['group_id'] ?? null;
        if (!$groupId) {
            echo json_encode(['success' => false, 'message' => 'Group ID required']);
            return;
        }
        $sql = "SELECT is_admin FROM group_members WHERE group_id = :group_id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':group_id' => $groupId, ':user_id' => $_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'is_admin' => $result['is_admin'] ?? false]);
    }
}
?>