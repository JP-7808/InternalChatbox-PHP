<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/GroupMemberInterface.php';

class GroupMember implements GroupMemberInterface {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getGroupMembers($groupId) {
        $sql = "SELECT u.id, u.name, u.designation, u.location, u.profileImage, gm.is_admin 
                FROM group_members gm 
                JOIN users u ON gm.user_id = u.id 
                WHERE gm.group_id = :group_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':group_id' => $groupId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>