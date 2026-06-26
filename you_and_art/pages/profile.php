<?php require_once '../php/db.php'; ?>

<?php
if(!isLoggedIn()) {
    header('Location: auth.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();
?>

<?php include '../header.php'; ?>

<div class="profile-container">
    <h2>Личный кабинет</h2>
    
    <div class="user-info">
        <h3>Информация о вас</h3>
        <p><strong>Имя:</strong> <?= htmlspecialchars($_SESSION['user_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user_email']) ?></p>
    </div>
    
    <div class="bookings-history">
        <h3>История бронирований</h3>
        <?php if(empty($bookings)): ?>
            <p>У вас пока нет бронирований</p>
            <a href="events.php"><button>Перейти к афише</button></a>
        <?php else: ?>
            <?php foreach($bookings as $booking): 
                $stmt2 = $pdo->prepare("SELECT title, date FROM events WHERE id = ?");
                $stmt2->execute([$booking['event_id']]);
                $event = $stmt2->fetch();
            ?>
            <div class="booking-card">
                <p><strong><?= htmlspecialchars($event['title']) ?></strong></p>
                <p><?= date('d.m.Y', strtotime($event['date'])) ?></p>
                <p><?= $booking['quantity'] ?> билетов</p>
                <p><?= number_format($booking['total_price'], 0, '', ' ') ?> ₽</p>
                <p>Статус: <?= $booking['status'] == 'paid' ? 'Оплачен' : 'Ожидает' ?></p>
                <p>Номер заказа: <?= $booking['order_number'] ?></p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../footer.php'; ?>