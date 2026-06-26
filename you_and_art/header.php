<?php require_once 'php/db.php'; ?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You and Art - Культурный центр</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
    <?php
    $root = '';
    if (strpos($_SERVER['SCRIPT_NAME'], '/pages/') !== false) {
        $root = '../';
    }
    ?>
    <link rel="stylesheet" href="<?= $root ?>css/style.css">
</head>

<body>
    <header>
        <div class="logo">
            <h1>You and Art</h1>
        </div>
        <nav>
            <a href="../index.php">Главная</a>
            <a href="../pages/events.php">Афиша</a>
            <a href="../pages/about.php">О нас</a>
            <a href="../pages/contacts.php">Контакты</a>
            <?php if (isLoggedIn()): ?>
                <a href="../pages/profile.php"><?= htmlspecialchars($_SESSION['user_name']) ?></a>
                <a href="../pages/cart.php">Корзина (<span id="cartCount"><?= getCartCount() ?></span>)</a>
                <?php if (isAdmin()): ?>
                    <a href="../pages/admin.php">Админка</a>
                <?php endif; ?>
                <a href="../pages/auth.php?logout=1">Выйти</a>
            <?php else: ?>
                <a href="../pages/auth.php">Вход/Регистрация</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>