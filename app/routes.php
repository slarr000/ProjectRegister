<?php
use ProjectRegister\Controllers\AuthController;
use ProjectRegister\Middleware\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

// Главная страница и аутентификация
$app->get('/', [AuthController::class, 'showHome'])->setName('home');
$app->get('/register', [AuthController::class, 'showRegister'])->setName('register');
$app->get('/login', [AuthController::class, 'showLogin'])->setName('login');
$app->get('/logout', [AuthController::class, 'logout'])->setName('logout');

// API endpoints (только POST)
$app->post('/api/register', [AuthController::class, 'register']);
$app->post('/api/login', [AuthController::class, 'login']);

// Защищенные маршруты
$app->group('', function (RouteCollectorProxy $group) {
    $group->get('/dashboard', [AuthController::class, 'dashboard'])->setName('dashboard');
})->add(new AuthMiddleware());
