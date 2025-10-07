<?php
namespace ProjectRegister\Controllers;

use ProjectRegister\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDOException;

class AuthController
{
    private $user;
    private $viewsPath;
    private  $validator;


   public function __construct(User $user, AuthValidator $validator = null)
    {
        $this->user = $user;
        $this->validator = $validator;
        $this->viewsPath = __DIR__ . '/../../views/';
    }

    public function showHome(Request $request, Response $response): Response
    {
        if (isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        }
        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    public function showRegister(Request $request, Response $response): Response
    {
        if (isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        }

        ob_start();
        include $this->viewsPath . 'register.php';
        $content = ob_get_clean();

        $response->getBody()->write($content);
        return $response;
    }

    public function showLogin(Request $request, Response $response): Response
    {
        if (isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        }

        ob_start();
        include $this->viewsPath . 'login.php';
        $content = ob_get_clean();

        $response->getBody()->write($content);
        return $response;
    }

    public function dashboard(Request $request, Response $response): Response
    {
        // Убедимся, что пользователь авторизован
        if (!isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        ob_start();
        include $this->viewsPath . 'dashboard.php';
        $content = ob_get_clean();

        $response->getBody()->write($content);
        return $response;
    }

    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        try {
            $user = $this->user->findByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                // Устанавливаем параметры сессии
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['username'] = $user['username'];

                // Регенерируем ID сессии для безопасности
                session_regenerate_id(true);

                return $this->jsonResponse($response, [
                    'success' => true,
                    'message' => 'Авторизация успешна!',
                    'redirect' => '/dashboard'
                ]);
            }

            return $this->jsonResponse($response, [
                'success' => false,
                'error' => 'Неверный логин или пароль'
            ]);

        } catch (Exception $e) {
            error_log("Login Error: " . $e->getMessage());
            return $this->jsonResponse($response, [
                'success' => false,
                'error' => 'Внутренняя ошибка сервера'
            ]);
        }
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        try {
            // Валидация
            if (empty($username) || empty($password)) {
                return $this->jsonResponse($response, [
                    'success' => false,
                    'error' => 'Логин и пароль обязательны'
                ]);
            }

            if (!preg_match('/^[a-zA-Z0-9]{2,20}$/', $username)) {
                return $this->jsonResponse($response, [
                    'success' => false,
                    'error' => 'Логин должен содержать 2-20 латинских символов и цифр'
                ]);
            }

            if (strlen($password) < 5 || !preg_match('/[a-zA-Z]/', $password)) {
                return $this->jsonResponse($response, [
                    'success' => false,
                    'error' => 'Пароль должен быть не менее 5 символов и содержать буквы'
                ]);
            }

            // Проверка существования пользователя
            if ($this->user->findByUsername($username)) {
                return $this->jsonResponse($response, [
                    'success' => false,
                    'error' => 'Пользователь с таким логином уже существует'
                ]);
            }

            // Создание пользователя
            if ($this->user->create($username, $password)) {
                return $this->jsonResponse($response, [
                    'success' => true,
                    'message' => 'Регистрация успешна!',
                    'redirect' => '/login'
                ]);
            }

            return $this->jsonResponse($response, [
                'success' => false,
                'error' => 'Ошибка при регистрации'
            ]);

        } catch (PDOException $e) {
            error_log("MySQL Register Error: " . $e->getMessage());

            // Проверка на дублирование пользователя
            if ($e->getCode() == '23000' || strpos($e->getMessage(), 'Duplicate') !== false) {
                return $this->jsonResponse($response, [
                    'success' => false,
                    'error' => 'Пользователь с таким логином уже существует'
                ]);
            }

            return $this->jsonResponse($response, [
                'success' => false,
                'error' => 'Ошибка базы данных'
            ]);
        } catch (Exception $e) {
            error_log("Register Error: " . $e->getMessage());
            return $this->jsonResponse($response, [
                'success' => false,
                'error' => 'Внутренняя ошибка сервера'
            ]);
        }
    }

    public function logout(Request $request, Response $response): Response
    {
        // Очищаем сессию
        $_SESSION = [];

        // Уничтожаем сессию
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        // Удаляем cookie сессии
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    private function jsonResponse(Response $response, array $data): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
