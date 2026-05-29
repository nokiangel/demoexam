<?php
session_start();
if(!isset($_SESSION['user_id'])) die('Чтобы посмотреть историю заявок, надо войти в аккаунт.');
include('db.php');

// Код изменения отзыва
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['review'])) {
    $review = $con->real_escape_string($_POST['review']);
    $user_id = (int)$_SESSION['user_id'];
    $con->query("UPDATE users SET review='$review' WHERE id='$user_id'");
    echo '<div style="color:green; padding:10px; background:#e6ffe6; margin-bottom:10px;">✓ Отзыв оставлен</div>';
}

// Код истории заявок
$user_id = (int)$_SESSION['user_id'];
$query = $con->query("SELECT * FROM request WHERE user_id='$user_id' ORDER BY date DESC");
if(!$query) die('query error: ' . $con->error); 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет - история заявок</title>
    
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn-home"> На главную</a>
        
        <h1>История заявок</h1>
        
        <?php
        $i = 0;
        if($query->num_rows == 0) {
            echo '<p style="text-align:center; color:#666;">У вас пока нет заявок.</p>';
        }
        while($request = $query->fetch_assoc()) {
            $i++; 
            echo '
            <div class="request">
                <h2>Заявка ' . $i . '</h2>
                <b>Дата: </b>' . htmlspecialchars($request['date']) . '<br>
                <b>Вид услуги: </b>' . htmlspecialchars($request['curses']) . '<br>
                <b>Тип оплаты: </b>' . htmlspecialchars($request['payment']) . '<br><br>
                <b>Статус: </b>' . htmlspecialchars($request['status']) . '<br>';
                
            if($request['status'] === 'Обучение завершено') {
                echo '
                <div class="review-form">
                    <form action="" method="POST">
                        <input type="text" name="review" placeholder="Отзыв об услуге" value="' . htmlspecialchars($request['review']) . '">
                        <button type="submit"> Оставить отзыв</button>
                    </form>
                </div>';
            }
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>