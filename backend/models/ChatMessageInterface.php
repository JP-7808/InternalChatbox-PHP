<?php
interface ChatMessageInterface {
    public function sendMessage($senderId, $receiverId, $groupId, $messageText);
    public function editMessage($messageId, $messageText);
    public function deleteMessage($messageId);
    public function getMessages($userId, $receiverId, $groupId);
}
?>