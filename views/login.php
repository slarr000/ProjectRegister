<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="ProjectRegister - система аутентификации и регистрации">
	<title>ProjectRegister - Авторизация</title>

	<!-- Preconnect для CDN -->
	<link rel="preconnect" href="https://cdn.jsdelivr.net">
	<link rel="preconnect" href="https://cdnjs.cloudflare.com">


	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
		  integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

	<!-- Кастомные стили -->
	<link href="/assets/style/style.css" rel="stylesheet">

	<style>
        .auth-body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .auth-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
        }
        .brand-logo {
            font-size: 2.5rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border: none;
            border-radius: 10px;
        }
	</style>
</head>
<body class="auth-body">
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-6 col-lg-5 col-xl-4">
			<div class="card auth-card">
				<div class="card-body p-4 p-md-5">
					<!-- Заголовок -->
					<div class="text-center mb-4">
						<div class="brand-logo">
							<i class="fas fa-project-diagram"></i>
						</div>
						<h2 class="card-title fw-bold text-dark mb-2">ProjectRegister</h2>
						<p class="text-muted">Войдите в свой аккаунт</p>
					</div>

					<!-- Сообщения об ошибках из URL -->
                    <?php
                    $error = $_GET['error'] ?? '';
                    if ($error): ?>
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<i class="fas fa-exclamation-triangle me-2"></i>
                            <?= htmlspecialchars($error) ?>
							<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
						</div>
                    <?php endif; ?>

					<!-- Форма входа -->
					<form id="loginForm" novalidate>
						<div class="mb-3">
							<label for="username" class="form-label fw-medium">
								<i class="fas fa-user me-1"></i>Логин
							</label>
							<input type="text"
								   class="form-control form-control-lg"
								   id="username"
								   name="username"
								   placeholder="Введите ваш логин"
								   required
								   autocomplete="username">
							<div class="invalid-feedback">Пожалуйста, введите логин</div>
						</div>

						<div class="mb-4">
							<label for="password" class="form-label fw-medium">
								<i class="fas fa-lock me-1"></i>Пароль
							</label>
							<input type="password"
								   class="form-control form-control-lg"
								   id="password"
								   name="password"
								   placeholder="Введите ваш пароль"
								   required
								   autocomplete="current-password">
							<div class="invalid-feedback">Пожалуйста, введите пароль</div>
						</div>

						<button type="submit" class="btn btn-primary w-100 btn-lg position-relative" id="submitBtn">
							<span class="btn-text">Войти в систему</span>
							<div class="spinner-border spinner-border-sm d-none position-absolute"
								 style="left: calc(50% - 0.5rem);"
								 role="status">
								<span class="visually-hidden">Загрузка...</span>
							</div>
						</button>
					</form>

					<!-- Ссылка на регистрацию -->
					<div class="text-center mt-4 pt-3 border-top">
						<p class="text-muted mb-2">Еще нет аккаунта?</p>
						<a href="/register" class="btn btn-outline-primary btn-sm">
							<i class="fas fa-user-plus me-1"></i>Создать аккаунт
						</a>
					</div>

					<!-- Контейнер для динамических сообщений -->
					<div id="message" class="mt-3"></div>
				</div>
			</div>

			<!-- Дополнительная информация -->
			<div class="text-center mt-4">
				<p class="text-white small opacity-75">
					<i class="fas fa-shield-alt me-1"></i>
					Ваши данные защищены
				</p>
			</div>
		</div>
	</div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

<!-- Кастомный JS -->
<script src="/assets/js/main.js"></script>

<script>
    // Базовая валидация формы
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = submitBtn.querySelector('.btn-text');
        const spinner = submitBtn.querySelector('.spinner-border');

        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                // Показываем индикатор загрузки
                btnText.classList.add('d-none');
                spinner.classList.remove('d-none');
                submitBtn.disabled = true;
            }

            form.classList.add('was-validated');
        });

        // Сбрасываем состояние кнопки при изменении полей
        form.addEventListener('input', function() {
            if (submitBtn.disabled) {
                btnText.classList.remove('d-none');
                spinner.classList.add('d-none');
                submitBtn.disabled = false;
            }
        });
    });
</script>
</body>
</html>
