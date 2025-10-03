<?php
namespace ProjectRegister\Validation;

class AuthValidator
{
    public function validateRegistration(array $data): array
    {
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($username) || empty($password)) {
            return ['success' => false, 'error' => 'Логин и пароль обязательны'];
        }

        if (!preg_match('/^[a-zA-Z0-9]{2,20}$/', $username)) {
            return ['success' => false, 'error' => 'Логин должен содержать 2-20 латинских символов и цифр'];
        }

        if (strlen($password) < 5 || !preg_match('/[a-zA-Z]/', $password)) {
            return ['success' => false, 'error' => 'Пароль должен быть не менее 5 символов и содержать буквы'];
        }

        return ['success' => true];
    }

    public function validateLogin(array $data): array
    {
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($username) || empty($password)) {
            return ['success' => false, 'error' => 'Логин и пароль обязательны'];
        }

        return ['success' => true];
    }
}