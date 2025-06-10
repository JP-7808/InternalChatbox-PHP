<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/ChatMessageInterface.php';

class ChatMessage implements ChatMessageInterface {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function sendMessage($senderId, $receiverId, $groupId, $messageText) {
        $sql = "INSERT INTO chat_messages (sender_id, receiver_id, group_id, message_text) 
                VALUES (:sender_id, :receiver_id, :group_id, :message_text)";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([
                ':sender_id' => $senderId,
                ':receiver_id' => $receiverId,
                ':group_id' => $groupId,
                ':message_text' => $messageText
            ]);
            return ['success' => true, 'message' => 'Message sent successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to send message: ' . $e->getMessage()];
        }
    }

    public function editMessage($messageId, $messageText) {
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
        $sql = "UPDATE chat_messages SET is_deleted = TRUE WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([':id' => $messageId]);
            return ['success' => true, 'message' => 'Message deleted successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Deletion failed: ' . $e->getMessage()];
        }
    }

    public function getMessages($userId, $receiverId, $groupId) {
        $sql = "SELECT cm.*, u.name AS sender_name 
                FROM chat_messages cm 
                JOIN users u ON cm.sender_id = u.id 
                WHERE (cm.receiver_id = :user_id AND cm.sender_id = :receiver_id) 
                   OR (cm.sender_id = :user_id AND cm.receiver_id = :receiver_id) 
                   OR (cm.group_id = :group_id)
                   AND cm.is_deleted = FALSE 
                ORDER BY cm.sent_at ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':receiver_id' => $receiverId,
            ':group_id' => $groupId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>