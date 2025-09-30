<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ProjectRegister - Личный кабинет</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="/assets/style/style.css" rel="stylesheet">
</head>
<body class="dashboard-body">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
	<div class="container">
		<a class="navbar-brand fw-bold" href="/dashboard">ProjectRegister</a>
		<div class="navbar-nav ms-auto">
			<a class="nav-link" href="/logout">Выйти</a>
		</div>
	</div>
</nav>

<div class="container mt-5">
	<div class="row justify-content-center">
		<div class="col-md-8 col-lg-6">
			<div class="card dashboard-card">
				<div class="card-body text-center p-5">
					<div class="mb-4">
						<h1 class="display-6 fw-bold text-primary">Добро пожаловать!</h1>
					</div>
					<p class="fw-bold fs-4 mb-4">
                        <span class="text-dark">
                            <?php
                            // Используем сессию вместо глобальной переменной
                            echo htmlspecialchars($_SESSION['username'] ?? 'Гость');
                            ?>
                        </span>, это ваш личный кабинет
					</p>
					<a href="/logout" class="btn btn-danger btn-lg px-4">
						Выйти
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>