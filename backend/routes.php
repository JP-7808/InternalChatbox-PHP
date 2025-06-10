<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/auth.php';
require_once __DIR__ . '/controllers/profile.php';
require_once __DIR__ . '/controllers/chat.php';
require_once __DIR__ . '/controllers/admin.php';

header('Content-Type: application/json');

$auth = new AuthController($pdo);
$profile = new ProfileController($pdo);
$chat = new ChatController($pdo);
$admin = new AdminController($pdo);

$request = $_SERVER['REQUEST_URI'];
$basePath = '/teamchat/backend';
$request = str_replace($basePath, '', $request);

error_log("Request URI: $request");

switch ($request) {
    case '/auth/register':
        $auth->register();
        break;
    case '/auth/login':
        $auth->login();
        break;
    case '/auth/logout':
        $auth->logout();
        break;
    case '/profile/get':
        $profile->getProfile();
        break;
    case '/profile/update':
        $profile->updateProfile();
        break;
    case '/profile/update_employee_id':
        $profile->updateEmployeeId();
        break;
    case '/profile/update_status':
        $profile->updateStatus();
        break;
    case '/chat/send':
        $chat->sendMessage();
        break;
    case '/chat/upload_file':
        $chat->uploadFile();
        break;
    case '/chat/create_group':
        $chat->createGroup();
        break;
    case '/chat/update_group':
        $chat->updateGroup();
        break;
    case '/chat/delete_group':
        $chat->deleteGroup();
        break;
    case '/chat/add_member':
        $chat->addGroupMember();
        break;
    case '/chat/remove_member':
        $chat->removeGroupMember();
        break;
    case '/chat/get_messages':
        $chat->getMessages();
        break;
    case '/chat/edit_message':
        $chat->editMessage();
        break;
    case '/chat/delete_message':
        $chat->deleteMessage();
        break;
    case '/chat/get_users':
        $chat->getUsers();
        break;
    case '/chat/get_groups':
        $chat->getGroups();
        break;
    case '/chat/get_group_members':
        $chat->getGroupMembers();
        break;
    case '/admin/dashboard':
        $admin->dashboard();
        break;
    case '/admin/edit_user':
        $admin->editUser();
        break;
    case '/admin/delete_user':
        $admin->deleteUser();
        break;
    case '/admin/change_password':
        $admin->changePassword();
        break;
    case '/admin/view_group_chat':
        $admin->viewGroupChat();
        break;
    case '/admin/download_group_chat':
        $admin->downloadGroupChat();
        break;
    case '/admin/view_private_chat':
        $admin->viewPrivateChat();
        break;
    case '/admin/download_private_chat':
        $admin->downloadPrivateChat();
        break;
    case '/admin/toggle_group_admin':
        $admin->toggleGroupAdmin();
        break;
    case '/test':
        echo json_encode(['success' => true, 'message' => 'Test endpoint reached']);
        break;
    case '/chat/check_group_admin':
        $chat->checkGroupAdmin();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid endpoint']);
        break;
}
?>