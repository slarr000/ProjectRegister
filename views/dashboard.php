<!DOCTYPE html>
<html lang="ru" class="h-100">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Личный кабинет ProjectRegister - система регистрации и аутентификации">
	<title>ProjectRegister - Личный кабинет</title>

	<!-- Bootstrap 5.3 с integrity hash -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
		  integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

	<!-- Font Awesome для иконок -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

	<link href="/assets/style/style.css" rel="stylesheet">

	<style>
        .user-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 1.5rem;
        }
        .info-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: none;
            border-radius: 15px;
        }
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .welcome-animation {
            animation: fadeInUp 0.8s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .timezone-badge {
            font-size: 0.7rem;
            background: #6c757d;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 5px;
        }
	</style>
</head>
<body class="dashboard-body d-flex flex-column min-vh-100">
<!-- Навигация -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
	<div class="container">
		<a class="navbar-brand fw-bold d-flex align-items-center" href="/dashboard">
			<i class="fas fa-project-diagram me-2"></i>
			ProjectRegister
		</a>

		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
				aria-controls="navbarNav" aria-expanded="false" aria-label="Переключить навигацию">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarNav">
			<div class="navbar-nav ms-auto">
                    <span class="nav-item nav-link disabled d-none d-md-block">
                        <i class="fas fa-user me-1"></i>
                        <?= htmlspecialchars($_SESSION['username'] ?? 'Гость') ?>
                    </span>
				<a class="nav-link" href="/logout" aria-label="Выйти из системы">
					<i class="fas fa-sign-out-alt me-1"></i>Выйти
				</a>
			</div>
		</div>
	</div>
</nav>

<!-- Основной контент -->
<main class="flex-grow-1 py-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 col-md-10 col-lg-8 col-xl-6">
				<!-- Карточка приветствия -->
				<div class="card dashboard-card border-0 shadow-lg welcome-animation">
					<div class="card-body text-center p-4 p-md-5">
						<!-- Аватар и приветствие -->
						<div class="mb-4">
							<div class="user-avatar">
								<i class="fas fa-user fa-lg"></i>
							</div>
							<h1 class="h2 fw-bold text-primary mb-2">Добро пожаловать!</h1>
							<p class="lead text-muted">
                                    <span class="fw-semibold text-dark username-highlight">
                                        <?= htmlspecialchars($_SESSION['username'] ?? 'Гость') ?>
                                    </span>, это ваш личный кабинет
							</p>
						</div>

						<!-- Информационная панель -->
						<div class="row g-3 mb-4">
							<div class="col-6">
								<div class="info-card p-3 rounded-3 border text-center">
									<i class="fas fa-calendar-check text-primary fa-lg mb-2"></i>
									<div class="fw-bold fs-5 current-date">
                                        <?php
                                        // Московское время для даты
                                        $moscowTime = new DateTime('now', new DateTimeZone('Europe/Moscow'));
                                        echo $moscowTime->format('d.m.Y');
                                        ?>
									</div>
									<small class="text-muted">Сегодня</small>
								</div>
							</div>
							<div class="col-6">
								<div class="info-card p-3 rounded-3 border text-center">
									<i class="fas fa-clock text-success fa-lg mb-2"></i>
									<div class="fw-bold fs-5 current-time">
                                        <?php
                                        // Московское время
                                        $moscowTime = new DateTime('now', new DateTimeZone('Europe/Moscow'));
                                        echo $moscowTime->format('H:i');
                                        ?>
										<span class="timezone-badge">MSK</span>
									</div>
									<small class="text-muted">Московское время</small>
								</div>
							</div>
						</div>

						<!-- Действия -->
						<div class="d-grid gap-2 d-md-flex justify-content-md-center">
							<a href="/logout" class="btn btn-danger btn-lg px-4 py-2 logout-btn" role="button">
								<i class="fas fa-sign-out-alt me-2"></i>Выйти из системы
							</a>
						</div>
					</div>
				</div>

				<!-- Дополнительная информация -->
				<div class="mt-4 text-center">
					<p class="text-muted small">
						<i class="fas fa-info-circle me-1"></i>
						Ваш аккаунт создан для безопасного доступа к системе
					</p>
				</div>
			</div>
		</div>
	</div>
</main>

<!-- Подвал -->
<footer class="bg-light py-3 mt-auto">
	<div class="container text-center">
		<p class="text-muted mb-0 small">
			&copy; <?= date('Y') ?> ProjectRegister. Все права защищены.
		</p>
	</div>
</footer>

<!-- Скрипты -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script>
    // Функция для обновления московского времени в реальном времени
    function updateMoscowTime() {
        const now = new Date();

        // Москва UTC+3 (летнее время может быть UTC+4)
        // Для простоты используем фиксированное смещение +3 часа
        const moscowOffset = 3 * 60 * 60 * 1000; // 3 часа в миллисекундах
        const moscowTime = new Date(now.getTime() + moscowOffset);

        const timeElement = document.querySelector('.current-time');
        if (timeElement) {
            const hours = moscowTime.getUTCHours().toString().padStart(2, '0');
            const minutes = moscowTime.getUTCMinutes().toString().padStart(2, '0');
            timeElement.innerHTML = `${hours}:${minutes} <span class="timezone-badge">MSK</span>`;
        }
    }

    // Обновляем время сразу при загрузке
    document.addEventListener('DOMContentLoaded', function() {
        updateMoscowTime();
        // Обновляем время каждую минуту
        setInterval(updateMoscowTime, 60000);
    });
</script>
</body>
</html>
