<?php
session_start();
if (!isset($_SESSION['user_id'])) die('Чтобы оставить заявку, надо войти в аккаунт.');

$success = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $review = $_POST['review'];
    $date = $_POST['date'];
    $curses = $_POST['curses'];
    $payment = $_POST['payment'];
    $status = 'Новая'; // Статус устанавливается автоматически
    
    include('db.php');
    
    // Для безопасности в реальном проекте используйте подготовленные выражения (prepared statements)
    $user_id = (int)$_SESSION['user_id']; // Защита от SQL-инъекций
    $review = $con->real_escape_string($review);
    $curses = $con->real_escape_string($curses);
    $payment = $con->real_escape_string($payment);
    
    $query = $con->query("INSERT INTO request (review, date, curses, payment, user_id, status) 
                          VALUES ('$review', '$date', '$curses', '$payment', '$user_id', '$status')");
    
    if (!$query) {
        $error = true;
        $error_msg = 'Ошибка: ' . $con->error;
    } else {
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание заявки - Водить.РФ</title>
    
</head>
<body>
    <div class="container">
        <!-- Кнопки навигации -->
        <div class="nav-buttons">
            <a href="index.php" class="btn-nav"> Главная</a>
            <a href="history.php" class="btn-nav"> История заявок</a>
        </div>
        
        <h1> Создание заявки</h1>

        <?php if ($success): ?>
            <div class="success-message">
                 Заявка успешно отправлена!<br><br>
                <a href="history.php"> Перейти к истории моих заявок →</a>
                <br><br>
                 Спасибо, что выбрали нас!
            </div>
        <?php elseif ($error): ?>
            <div class="error-message">
                 Ошибка при отправке заявки: <?php echo htmlspecialchars($error_msg); ?><br>
                <a href="javascript:history.back()">◀ Попробовать снова</a>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST" action="" id="requestForm">
            
            <label for="curses">🚤 Название курса</label>
            <select id="curses" name="curses" required>
                <option value="Курсы повышения
квалификации">Курсы повышения
квалификации</option>
                <option value="Курсы переподготовки">Курсы переподготовки</option>
                <option value="Курсы по охране труда">Курсы по охране труда</option>
               
            </select>

            <label for="date"> Когда желаете начать обучение?</label>
            <input id="date" type="datetime-local" name="date" required>

            <label for="payment"> Способ оплаты</label>
            <select id="payment" name="payment" required>
                <option value="наличные">Наличные</option>
                <option value="перевод">Переводом по номеру</option>
                <option value="карта">Банковской картой</option>
            </select>

            <label for="review"> Дополнительная информация</label>
            <textarea id="review" name="review" placeholder="Опишите ваши пожелания или комментарий..."></textarea>
             
            <button type="submit" id="submitBtn"> Отправить заявку</button>
        </form>
        <?php endif; ?>
    </div>

    <script>
        // Анимация при отправке формы
        const form = document.getElementById('requestForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                // Добавляем класс загрузки на кнопку
                submitBtn.classList.add('loading');
                submitBtn.textContent = 'Отправка';
            });
        }

        // Анимация при фокусе на полях
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.style.transition = 'all 0.3s ease';
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.style.transform = 'scale(1)';
                }
            });
        });
    </script>
</body>
</html>