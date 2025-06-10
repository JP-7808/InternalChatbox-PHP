<?php
interface ChatGroupInterface {
    public function createGroup($groupName, $creatorId);
    public function updateGroup($groupId, $groupName);
    public function deleteGroup($groupId);
    public function addGroupMember($groupId, $userId, $isAdmin);
    public function removeGroupMember($groupId, $userId);
    public function toggleGroupAdmin($groupId, $userId, $isAdmin);
}
?>