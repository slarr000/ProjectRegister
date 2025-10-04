<?php
namespace ProjectRegister\Models;

use PDO;
use PDOException;

class User
{
    private $db;
    private $table = 'users';

    public function __construct()
    {
        try {
            // Подключаем конфигурацию
            $configPath = __DIR__ . '/../../app/config.php';
            if (!file_exists($configPath)) {
                throw new \Exception("Config file not found: " . $configPath);
            }
            require_once $configPath;

            // Проверяем, что константы определены
            if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
                throw new \Exception("Database configuration constants are not defined");
            }

            // Создаем подключение
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            if (defined('DB_PORT') && DB_PORT) {
                $dsn .= ";port=" . DB_PORT;
            }

            $this->db = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);

            error_log("✅ Database connected successfully to: " . DB_NAME);

        } catch (PDOException $e) {
            $error = "❌ Database connection failed: " . $e->getMessage();
            error_log($error);
            throw new \Exception($error);
        } catch (\Exception $e) {
            error_log("❌ Configuration error: " . $e->getMessage());
            throw $e;
        }
    }

    
    public function findByUsername($username)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {
                error_log("✅ User found: " . $username);
            } else {
                error_log("❌ User not found: " . $username);
            }

            return $user;

        } catch (PDOException $e) {
            error_log("❌ Database error in findByUsername: " . $e->getMessage());
            throw new \Exception("Database error");
        }
    }

    public function findById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("❌ Database error in findById: " . $e->getMessage());
            return false;
        }
    }

    public function create($username, $password)
    {
        try {
            // Хешируем пароль
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            if ($hashedPassword === false) {
                throw new \Exception("Password hashing failed");
            }

            // Вставляем пользователя
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (username, password) VALUES (?, ?)");
            $result = $stmt->execute([$username, $hashedPassword]);

            if ($result) {
                $userId = $this->db->lastInsertId();
                error_log("✅ User created successfully: " . $username . " (ID: " . $userId . ")");
                return $userId;
            }

            error_log("❌ User creation failed: " . $username);
            return false;

        } catch (PDOException $e) {
            // Ошибка дублирования пользователя
            if ($e->getCode() == '23000') {
                error_log("❌ User already exists: " . $username);
                return false;
            }

            error_log("❌ Database error in create: " . $e->getMessage());
            throw new \Exception("Database error during user creation");
        }
    }

    public function getAllUsers()
    {
        try {
            $stmt = $this->db->query("SELECT id, username, created_at FROM {$this->table} ORDER BY created_at DESC");
            $users = $stmt->fetchAll();
            error_log("✅ Retrieved " . count($users) . " users from database");
            return $users;
        } catch (PDOException $e) {
            error_log("❌ Database error in getAllUsers: " . $e->getMessage());
            return [];
        }
    }

    // Проверка существования таблицы
    public function checkTableExists()
    {
        try {
            $stmt = $this->db->query("SELECT 1 FROM {$this->table} LIMIT 1");
            return true;
        } catch (PDOException $e) {
            error_log("❌ Table check failed: " . $e->getMessage());
            return false;
        }
    }
}
