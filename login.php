<?php
session_start();

// Если пользователь уже авторизован, перенаправляем
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['admin']) && $_SESSION['admin']) {
        header('Location: admin.php');
    } else {
        header('Location: create.php');
    }
    exit;
}

$error = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    
    if (empty($login) || empty($password)) {
        $error = true;
        $error_message = 'Пожалуйста, заполните все поля';
    } else {
        include('db.php');
        
        // Используем подготовленные выражения для защиты от SQL инъекций
        $stmt = $con->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $error = true;
            $error_message = 'Неверный логин или пароль';
        } else {
            $user = $result->fetch_assoc();
            
            // Проверка пароля (рекомендуется использовать password_hash() при регистрации)
            if ($password !== $user['password']) {
                $error = true;
                $error_message = 'Неверный логин или пароль';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_login'] = $user['login'];
                $_SESSION['user_fullname'] = $user['fullname'];
                
                // Проверка на администратора
                if ($user['login'] == 'Admin26') {
                    $_SESSION['admin'] = true;
                    header('Location: admin.php');
                } else {
                    header('Location: create.php');
                }
                exit;
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Учусь.РФ</title>
    
</head>
<body>
    <div class="wave"></div>
    
    <div class="container">
        <div class="logo">
            <h1>Учусь.РФ</h1>
            <p>Курсы повышения квалификации</p>
        </div>

        <div class="form-header">
            <h2>Добро пожаловать!</h2>
            <p>Войдите в свой аккаунт</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <span>⚠️</span>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="loginForm">
            <div class="form-group">
                <label for="login">
                    <span class="icon">👤</span> Логин
                </label>
                <input type="text" id="login" name="login" 
                       value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>"
                       placeholder="Введите ваш логин" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">
                    <span class="icon">🔒</span> Пароль
                </label>
                <input type="password" id="password" name="password" 
                       placeholder="Введите пароль" required>
            </div>

            <button type="submit" class="btn-login" id="submitBtn">
                <span class="icon">🚀</span> Войти
            </button>
        </form>

        <div class="form-footer">
            <p>Нет аккаунта? <a href="register.php" class="register-link">Зарегистрироваться →</a></p>
            <a href="index.php" class="back-home">← Вернуться на главную</a>
        </div>
    </div>

    <script>
        // Анимация при отправке формы
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                const login = document.getElementById('login').value.trim();
                const password = document.getElementById('password').value;
                
                if (!login || !password) {
                    e.preventDefault();
                    showError('Пожалуйста, заполните все поля');
                    return;
                }
                
                // Добавляем анимацию загрузки
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="icon">⏳</span> Вход...';
                submitBtn.style.opacity = '0.7';
                submitBtn.disabled = true;
                
                // Если форма валидна, она отправится автоматически
                setTimeout(() => {
                    // Эта функция выполнится только если форма не отправилась
                    submitBtn.innerHTML = originalText;
                    submitBtn.style.opacity = '1';
                    submitBtn.disabled = false;
                }, 3000);
            });
        }
        
        // Функция показа ошибки (клиентская валидация)
        function showError(message) {
            const existingError = document.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.innerHTML = `<span>⚠️</span> ${message}`;
            
            const formHeader = document.querySelector('.form-header');
            formHeader.insertAdjacentElement('afterend', errorDiv);
            
            // Анимация встряхивания контейнера
            const container = document.querySelector('.container');
            container.style.animation = 'shakeError 0.5s ease-in-out';
            setTimeout(() => {
                container.style.animation = '';
            }, 500);
        }
        
        // Добавляем эффект при наведении на инпуты
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateX(5px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateX(0)';
            });
        });
        
        // Сохраняем логин в localStorage (для удобства, опционально)
        const savedLogin = localStorage.getItem('savedLogin');
        if (savedLogin && !document.getElementById('login').value) {
            document.getElementById('login').value = savedLogin;
        }
        
        form.addEventListener('submit', function() {
            const login = document.getElementById('login').value;
            localStorage.setItem('savedLogin', login);
        });
    </script>
</body>
</html>