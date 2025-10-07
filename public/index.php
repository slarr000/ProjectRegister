<?php
require __DIR__ . '/../vendor/autoload.php';

use ProjectRegister\Models\User;
use ProjectRegister\Controllers\AuthController;
use ProjectRegister\Validation\AuthValidator;
use ProjectRegister\Middleware\AuthMiddleware;
use Slim\Psr7\Response;
use Slim\Psr7\Factory\ResponseFactory;

// Настройки ошибок и сессии
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
ini_set('error_log', $logDir . '/php-errors.log');

error_log("=== APP STARTED ===");

// Запускаем сессию ДО создания приложения
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$app = \Slim\Factory\AppFactory::create();
$responseFactory = new ResponseFactory();

// Инициализация зависимостей
try {
    // ИСПРАВЛЕНО: используем обычный конструктор вместо createFromConfig()
    $userModel = new User();
    $authValidator = new AuthValidator();
    $authController = new AuthController($userModel, $authValidator);
} catch (Exception $e) {
    error_log("❌ Dependency initialization failed: " . $e->getMessage());
    die("Application initialization failed. Check logs for details.");
}

// Middleware
$app->addBodyParsingMiddleware();

// Глобальный обработчик ошибок
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// CORS Middleware
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Вспомогательная функция для JSON ответов
function jsonResponse($response, $data, $status = 200) {
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
}

// Подключаем файл с маршрутами
require __DIR__ . '/../app/routes.php';
$app->run();
