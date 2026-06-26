<?php require_once '../php/db.php'; ?>

<?php
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_feedback'])) {
    $stmt = $pdo->prepare("INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['email'], $_POST['message']]);
    $success = "Сообщение отправлено!";
}
?>

<?php include '../header.php'; ?>

<div class="contacts-container">
    <h1>Контакты</h1>
    
    <div class="contacts-grid">
        <div class="contacts-info">
            <p><strong>Адрес:</strong> Москва, ул. Арбат, 10</p>
            <p><strong>Телефон:</strong> +7 (999) 123-45-67</p>
            <p><strong>Email:</strong> info@youandart.ru</p>
            <p><strong>Режим работы:</strong> Пн-Вс: 10:00 - 22:00</p>
        </div>
        
        <div class="feedback-form">
            <h3>Напишите нам</h3>
            <?php if(isset($success)): ?>
                <p class="success"><?= $success ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="name" placeholder="Ваше имя" required>
                <input type="email" name="email" placeholder="Email" required>
                <textarea name="message" placeholder="Сообщение" rows="5" required></textarea>
                <button type="submit" name="send_feedback">Отправить</button>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>