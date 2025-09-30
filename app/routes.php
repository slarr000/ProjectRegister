<?php
use ProjectRegister\Controllers\AuthController;
use ProjectRegister\Middleware\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

$app->get('/', [AuthController::class, 'showHome'])->setName('home');
$app->get('/register', [AuthController::class, 'showRegister'])->setName('register');
$app->post('/register', [AuthController::class, 'register']);
$app->get('/login', [AuthController::class, 'showLogin'])->setName('login');
$app->post('/login', [AuthController::class, 'login']);
$app->get('/logout', [AuthController::class, 'logout'])->setName('logout');

// Защищенные маршруты
$app->group('', function (RouteCollectorProxy $group) {
    $group->get('/dashboard', [AuthController::class, 'dashboard'])->setName('dashboard');
})->add(new AuthMiddleware());