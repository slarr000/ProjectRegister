<?php
use ProjectRegister\Controllers\AuthController;
use ProjectRegister\Middleware\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

// Получаем экземпляры из контейнера или глобальной области
global $authController, $authMiddleware;

// Главная страница и аутентификация
$app->get('/', [$authController, 'showHome'])->setName('home');
$app->get('/register', [$authController, 'showRegister'])->setName('register');
$app->get('/login', [$authController, 'showLogin'])->setName('login');
$app->get('/logout', [$authController, 'logout'])->setName('logout');

// API endpoints (только POST)
$app->post('/api/register', [$authController, 'register']);
$app->post('/api/login', [$authController, 'login']);

// Защищенные маршруты
$app->group('', function (RouteCollectorProxy $group) use ($authController) {
    $group->get('/dashboard', [$authController, 'dashboard'])->setName('dashboard');
})->add($authMiddleware);
