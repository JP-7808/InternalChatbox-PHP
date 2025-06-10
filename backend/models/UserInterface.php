<?php
interface UserInterface {
    public function register($data);
    public function login($email, $password);
    public function getProfile($userId);
    public function updateProfile($userId, $data);
    public function updateEmployeeId($userId, $employeeId);
    public function updateStatus($userId, $status);
}
?>