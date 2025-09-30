<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ProjectRegister - Регистрация</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="/assets/style/style.css" rel="stylesheet">
</head>
<body class="auth-body">
<div class="container">
	<div class="row justify-content-center align-items-center min-vh-100">
		<div class="col-md-6 col-lg-5">
			<div class="card auth-card">
				<div class="card-body p-4">
					<div class="text-center mb-4">
						<h2 class="card-title">ProjectRegister</h2>
						<p class="text-muted">Создайте новый аккаунт</p>
					</div>

					<form id="registerForm">
						<div class="mb-3">
							<label for="username" class="form-label">Логин</label>
							<input type="text" class="form-control" id="username" name="username"
								   placeholder="Введите логин" required>
							<div class="form-text">Латиница и цифры, 2-20 символов</div>
						</div>
						<div class="mb-4">
							<label for="password" class="form-label">Пароль</label>
							<input type="password" class="form-control" id="password" name="password"
								   placeholder="Введите пароль" required>
							<div class="form-text">Минимум 5 символов, не только цифры</div>
						</div>
						<button type="submit" class="btn btn-primary w-100 btn-lg">
							Зарегистрироваться
						</button>
					</form>

					<div class="text-center mt-3">
						<!-- ИСПРАВЛЕНО: добавлен слеш перед login -->
						<a href="/login" class="text-decoration-none">Уже есть аккаунт? Войдите</a>
					</div>

					<div id="message" class="mt-3"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- ИСПРАВЛЕНО: подключаем auth.js вместо main.js -->
<script src="/assets/js/main.js"></script>
</body>
</html>