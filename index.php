<?php
session_start();

// Выход из системы
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Проверяем, установлен ли ключ admin в сессии
$is_admin = isset($_SESSION['admin']) && $_SESSION['admin'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Учусь.РФ - дополнительное образование</title>
  
  <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body>
<!-- Шапка сайта -->
<header class="header">
  <div class="nav">
    <a href="index.php" class="logo">Учусь.РФ</a>

    <!-- Кнопки навигации -->
    <div class="nav-buttons">
      <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="login.php" class="btn-login">Войти</a>
        <a href="register.php" class="btn-register">Регистрация</a>
      <?php elseif ($is_admin): ?>
        <a href="admin.php" class="btn-admin">Панель администратора</a>
        <a href="?logout=1" class="btn-exit">Выход</a>
      <?php elseif (isset($_SESSION['user_id'])): ?>
        <a href="history.php" class="btn-lk">Мои заявки</a>
        <a href="create.php" class="btn-create">Новая заявка</a>
        <a href="?logout=1" class="btn-exit">Выход</a>
      <?php endif; ?>
    </div>
  </div>
</header>


<!-- Точки навигации -->
<div class="dot-container">
  <span class="dot" onclick="currentSlide(1)"></span>
  <span class="dot" onclick="currentSlide(2)"></span>
  <span class="dot" onclick="currentSlide(3)"></span>
  <span class="dot" onclick="currentSlide(4)"></span>
</div>

<!-- Основной контент -->
<section style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
  <h2 style="text-align: center; color: var(--silver); margin-bottom: 30px;">Почему выбирают нас?</h2>
  
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
    <div style="background: rgba(26, 58, 95, 0.8); padding: 25px; border-radius: 10px; text-align: center;">
      <h3 style="color: var(--silver-light);">Опытные преподаватели</h3>
      <p style="color: var(--silver); line-height: 1.5;">Все наши преподаватели имеют высшую категорию.</p>
    </div>
    
    <div style="background: rgba(26, 58, 95, 0.8); padding: 25px; border-radius: 10px; text-align: center;">
      <h3 style="color: var(--silver-light);">Современное оборудование</h3>
      <p style="color: var(--silver); line-height: 1.5;">Мы используем только новые программы для обучения.</p>
    </div>
    
    <div style="background: rgba(26, 58, 95, 0.8); padding: 25px; border-radius: 10px; text-align: center;">
      <h3 style="color: var(--silver-light);">Гибкий график</h3>
      <p style="color: var(--silver); line-height: 1.5;">Подберём удобное время для занятий под ваш график.</p>
    </div>
  </div>
</section>

<script>
// JavaScript для управления слайдером
let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");

  if (n > slides.length) { slideIndex = 1 }
  if (n < 1) { slideIndex = slides.length }

  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }

  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
}

// Автоматическое переключение слайдов каждые 3 секунды
let slideInterval = setInterval(function() {
  plusSlides(1);
}, 3000);

// Останавливаем автоматическое переключение при наведении на слайдер
const slideshowContainer = document.querySelector('.slideshow-container');
if (slideshowContainer) {
  slideshowContainer.addEventListener('mouseenter', function() {
    clearInterval(slideInterval);
  });
  
  slideshowContainer.addEventListener('mouseleave', function() {
    slideInterval = setInterval(function() {
      plusSlides(1);
    }, 3000);
  });
}
</script>
</body>
</html>