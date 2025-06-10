<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/ChatGroupInterface.php';

class ChatGroup implements ChatGroupInterface {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createGroup($groupName, $creatorId) {
        $sql = "INSERT INTO chat_groups (group_name) VALUES (:group_name)";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([':group_name' => $groupName]);
            $groupId = $this->pdo->lastInsertId();
            // Add creator as admin
            $this->addGroupMember($groupId, $creatorId, true);
            return ['success' => true, 'message' => 'Group created successfully', 'group_id' => $groupId];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to create group: ' . $e->getMessage()];
        }
    }

    public function updateGroup($groupId, $groupName) {
        $sql = "UPDATE chat_groups SET group_name = :group_name WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([':group_name' => $groupName, ':id' => $groupId]);
            return ['success' => true, 'message' => 'Group updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Update failed: ' . $e->getMessage()];
        }
    }

    public function deleteGroup($groupId) {
        $sql = "DELETE FROM chat_groups WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([':id' => $groupId]);
            return ['success' => true, 'message' => 'Group deleted successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Deletion failed: ' . $e->getMessage()];
        }
    }

    public function addGroupMember($groupId, $userId, $isAdmin = false) {
        $sql = "INSERT INTO group_members (group_id, user_id, is_admin) 
                VALUES (:group_id, :user_id, :is_admin)";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([
                ':group_id' => $groupId,
                ':user_id' => $userId,
                ':is_admin' => $isAdmin ? 1 : 0
            ]);
            return ['success' => true, 'message' => 'Member added successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to add member: ' . $e->getMessage()];
        }
    }

    public function removeGroupMember($groupId, $userId) {
        $sql = "DELETE FROM group_members WHERE group_id = :group_id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([':group_id' => $groupId, ':user_id' => $userId]);
            return ['success' => true, 'message' => 'Member removed successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to remove member: ' . $e->getMessage()];
        }
    }

    public function toggleGroupAdmin($groupId, $userId, $isAdmin) {
        $sql = "UPDATE group_members SET is_admin = :is_admin 
                WHERE group_id = :group_id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([
                ':is_admin' => $isAdmin ? 1 : 0,
                ':group_id' => $groupId,
                ':user_id' => $userId
            ]);
            return ['success' => true, 'message' => 'Admin status updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to update admin status: ' . $e->getMessage()];
        }
    }
}
?>