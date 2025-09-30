class Auth {
    static validateUsername(username) {
        const regex = /^[a-zA-Z0-9]{2,20}$/;
        return regex.test(username);
    }

    static validatePassword(password) {
        if (password.length < 5) return false;
        if (!/[a-zA-Z]/.test(password)) return false;
        return true;
    }

    static showMessage(elementId, message, isError = true) {
        const messageEl = document.getElementById(elementId);
        if (messageEl) {
            messageEl.innerHTML = message;
            messageEl.className = `mt-3 alert ${isError ? 'alert-danger' : 'alert-success'}`;
            setTimeout(() => {
                messageEl.innerHTML = '';
                messageEl.className = '';
            }, 5000);
        } else {
            alert(isError ? '❌ ' + message : '✅ ' + message);
        }
    }

    static setFormLoading(form, isLoading) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        if (isLoading) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Загрузка...';
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    static async handleResponse(response) {
        const contentType = response.headers.get('content-type');

        // Если ответ не JSON, это ошибка
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Non-JSON response:', text.substring(0, 200));
            throw new Error('Сервер вернул некорректный ответ. Попробуйте позже.');
        }

        const result = await response.json();

        // Если статус не 200-299, выбрасываем ошибку
        if (!response.ok) {
            throw new Error(result.error || 'Ошибка сервера');
        }

        return result;
    }

    static async register(username, password) {
        const form = document.getElementById('registerForm');

        try {
            console.log('=== REGISTRATION START ===');

            // Валидация на клиенте
            if (!this.validateUsername(username)) {
                this.showMessage('message', 'Логин должен содержать только латинские символы и цифры (2-20 символов)');
                return;
            }

            if (!this.validatePassword(password)) {
                this.showMessage('message', 'Пароль должен быть не менее 5 символов и содержать буквы');
                return;
            }

            this.setFormLoading(form, true);

            const response = await fetch('/api/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ username, password })
            });

            console.log('Response status:', response.status);

            const result = await this.handleResponse(response);
            console.log('Response data:', result);

            if (result.success) {
                this.showMessage('message', 'Регистрация успешна! Перенаправляем на страницу входа...', false);
                setTimeout(() => {
                    window.location.href = '/login';
                }, 1500);
            } else {
                this.showMessage('message', 'Ошибка регистрации: ' + (result.error || 'Неизвестная ошибка'));
            }
        } catch (error) {
            console.error('Registration error:', error);
            this.showMessage('message', error.message);
        } finally {
            if (form) this.setFormLoading(form, false);
        }
    }

    static async login(username, password) {
        const form = document.getElementById('loginForm');

        try {
            console.log('=== LOGIN START ===');

            if (!username || !password) {
                this.showMessage('message', 'Пожалуйста, заполните все поля');
                return;
            }

            this.setFormLoading(form, true);

            const response = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ username, password })
            });

            console.log('Response status:', response.status);

            const result = await this.handleResponse(response);
            console.log('Response data:', result);

            if (result.success) {
                this.showMessage('message', 'Вход выполнен успешно! Перенаправляем...', false);
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1000);
            } else {
                this.showMessage('message', 'Ошибка авторизации: ' + (result.error || 'Неизвестная ошибка'));
            }
        } catch (error) {
            console.error('Login error:', error);
            this.showMessage('message', error.message);
        } finally {
            if (form) this.setFormLoading(form, false);
        }
    }
}

// Обработчики форм
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up form handlers');

    const registerForm = document.getElementById('registerForm');
    const loginForm = document.getElementById('loginForm');

    // Валидация в реальном времени
    const usernameInputs = document.querySelectorAll('input[name="username"]');
    usernameInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = this.value;
            const isValid = Auth.validateUsername(value);

            if (value && !isValid) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else if (value && isValid) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
    });

    const passwordInputs = document.querySelectorAll('input[name="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = this.value;
            const isValid = Auth.validatePassword(value);

            if (value && !isValid) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else if (value && isValid) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
    });

    if (registerForm) {
        console.log('Register form found');
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            console.log('Register form submitted');
            Auth.register(username, password);
        });
    }

    if (loginForm) {
        console.log('Login form found');
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            console.log('Login form submitted');
            Auth.login(username, password);
        });
    }
});