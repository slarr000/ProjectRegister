<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ProjectRegister - Авторизация</title>
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
						<p class="text-muted">Войдите в свой аккаунт</p>
					</div>

					<form id="loginForm">
						<div class="mb-3">
							<label for="username" class="form-label">Логин</label>
							<input type="text" class="form-control" id="username" name="username"
								   placeholder="Введите логин" required>
						</div>
						<div class="mb-4">
							<label for="password" class="form-label">Пароль</label>
							<input type="password" class="form-control" id="password" name="password"
								   placeholder="Введите пароль" required>
						</div>
						<button type="submit" class="btn btn-primary w-100 btn-lg">
							Войти
						</button>
					</form>

					<div class="text-center mt-3">
						<a href="/register" class="text-decoration-none">Нет аккаунта? Зарегистрируйтесь</a>
					</div>

					<div id="message" class="mt-3"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="/assets/js/main.js"></script>
</body>
</html>