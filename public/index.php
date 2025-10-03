<?php
require __DIR__ . '/../vendor/autoload.php';


use ProjectRegister\Models\User;
use ProjectRegister\Controllers\AuthController;
use ProjectRegister\Validation\AuthValidator;
use ProjectRegister\Middleware\AuthMiddleware;
use Slim\Psr7\Response;
use Slim\Psr7\Factory\ResponseFactory;

// Настройка ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Создаем директорию для логов
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
ini_set('error_log', $logDir . '/php-errors.log');

error_log("=== APP STARTED ===");

session_start();

$app = \Slim\Factory\AppFactory::create();
$responseFactory = new ResponseFactory();

// Middleware для парсинга JSON
$app->addBodyParsingMiddleware();

// Улучшенный Middleware для обработки ошибок
$app->add(function ($request, $handler) use ($responseFactory) {
    try {
        return $handler->handle($request);
    } catch (Throwable $e) {
        error_log("Unhandled Error: " . $e->getMessage());
        $response = $responseFactory->createResponse();

        $errorData = ['error' => 'Internal server error'];
        if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
            $errorData['debug'] = $e->getMessage();
        }

        $response->getBody()->write(json_encode($errorData));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// CORS Middleware
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Helper function for JSON responses
function jsonResponse($response, $data, $status = 200) {
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
}

// ========== ОСНОВНЫЕ МАРШРУТЫ ==========

// Главная страница
$app->get('/', function ($request, $response) {
    if (isset($_SESSION['user_id'])) {
        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    } else {
        return $response->withHeader('Location', '/login')->withStatus(302);
    }
});

// Страница регистрации
$app->get('/register', function ($request, $response) {
    if (isset($_SESSION['user_id'])) {
        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }

    ob_start();
    include __DIR__ . '/../views/register.php';
    $content = ob_get_clean();
    $response->getBody()->write($content);
    return $response->withHeader('Content-Type', 'text/html');
});

// Страница логина
$app->get('/login', function ($request, $response) {
    if (isset($_SESSION['user_id'])) {
        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }

    ob_start();
    include __DIR__ . '/../views/login.php';
    $content = ob_get_clean();
    $response->getBody()->write($content);
    return $response->withHeader('Content-Type', 'text/html');
});

// Личный кабинет
$app->get('/dashboard', function ($request, $response) {
    if (!isset($_SESSION['user_id'])) {
        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    ob_start();
    include __DIR__ . '/../views/dashboard.php';
    $content = ob_get_clean();
    $response->getBody()->write($content);
    return $response->withHeader('Content-Type', 'text/html');
});

// API для регистрации
$app->post('/api/register', function ($request, $response) {
    try {
        error_log("Registration API called");

        $data = $request->getParsedBody();
        error_log("Registration data: " . print_r($data, true));

        if ($data === null) {
            return jsonResponse($response, ['error' => 'Invalid JSON data'], 400);
        }

        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        // Валидация
        if (empty($username) || empty($password)) {
            return jsonResponse($response, ['error' => 'Логин и пароль обязательны для заполнения'], 400);
        }

        if (!preg_match('/^[a-zA-Z0-9]{2,20}$/', $username)) {
            return jsonResponse($response, ['error' => 'Логин должен содержать только латинские символы и цифры (2-20 символов)'], 400);
        }

        if (strlen($password) < 5 || !preg_match('/[a-zA-Z]/', $password)) {
            return jsonResponse($response, ['error' => 'Пароль должен быть не менее 5 символов и содержать буквы'], 400);
        }

        $userModel = new User();

        // Проверка существования пользователя
        if ($userModel->findByUsername($username)) {
            return jsonResponse($response, ['error' => 'Пользователь с таким логином уже существует'], 400);
        }

        // Создание пользователя
        $userId = $userModel->create($username, $password);
        if ($userId) {
            return jsonResponse($response, [
                'success' => true,
                'message' => 'Регистрация успешна'
            ]);
        } else {
            return jsonResponse($response, ['error' => 'Ошибка при создании пользователя'], 500);
        }

    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        return jsonResponse($response, ['error' => 'Внутренняя ошибка сервера: ' . $e->getMessage()], 500);
    }
});

// API для логина
$app->post('/api/login', function ($request, $response) {
    try {
        $data = $request->getParsedBody();

        if ($data === null) {
            return jsonResponse($response, ['error' => 'Invalid JSON data'], 400);
        }

        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        // Базовая валидация
        if (empty($username) || empty($password)) {
            return jsonResponse($response, ['error' => 'Логин и пароль обязательны'], 400);
        }

        $userModel = new User();
        $user = $userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            // Устанавливаем сессию
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            return jsonResponse($response, [
                'success' => true,
                'message' => 'Авторизация успешна'
            ]);
        }

        error_log("Failed login attempt for username: " . $username);
        return jsonResponse($response, ['error' => 'Неверный логин или пароль'], 401);

    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        return jsonResponse($response, ['error' => 'Внутренняя ошибка сервера'], 500);
    }
});

// Логаут
$app->get('/logout', function ($request, $response) {
    // Очищаем сессию
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
    return $response->withHeader('Location', '/login')->withStatus(302);
});

// ========== ОТЛАДОЧНЫЕ МАРШРУТЫ ==========

// Простейший тест
$app->get('/debug/simple-test', function ($request, $response) {
    error_log("Simple test called");
    return jsonResponse($response, [
        'status' => 'OK',
        'message' => 'Slim works perfectly!',
        'timestamp' => time()
    ]);
});

// Тест подключения к базе данных
$app->get('/debug/db-connect-test', function ($request, $response) {
    error_log("DB connect test started");

    try {
        $userModel = new User();

        return jsonResponse($response, [
            'status' => 'SUCCESS',
            'message' => 'Database connection successful',
            'database' => DB_NAME,
            'table_exists' => $userModel->checkTableExists()
        ]);

    } catch (Exception $e) {
        error_log("DB connect test failed: " . $e->getMessage());
        return jsonResponse($response, [
            'status' => 'ERROR',
            'message' => 'Database connection failed',
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});

// Тест создания пользователя
$app->get('/debug/create-test-user', function ($request, $response) {
    try {
        $userModel = new User();
        $testUsername = 'testuser_' . time();
        $testPassword = 'test123';

        $userId = $userModel->create($testUsername, $testPassword);

        if ($userId) {
            return jsonResponse($response, [
                'status' => 'SUCCESS',
                'message' => 'Test user created',
                'user_id' => $userId,
                'username' => $testUsername
            ]);
        } else {
            return jsonResponse($response, [
                'status' => 'ERROR',
                'message' => 'Failed to create test user'
            ], 500);
        }

    } catch (Exception $e) {
        return jsonResponse($response, [
            'status' => 'ERROR',
            'message' => 'Error creating test user',
            'error' => $e->getMessage()
        ], 500);
    }
});

$app->run();
