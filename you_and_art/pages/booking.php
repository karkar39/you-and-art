<?php require_once '../php/db.php'; ?>

<?php
$event_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$event) {
    header('Location: events.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if(!isLoggedIn()) {
        header('Location: auth.php');
        exit;
    }
    $quantity = $_POST['quantity'];
    $stmt = $pdo->prepare("INSERT INTO cart (user_id, event_id, quantity) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $event_id, $quantity]);
    header('Location: cart.php');
    exit;
}
?>

<?php include '../header.php'; ?>

<div class="booking-container">
    <div class="booking-event-info">
        <h2><?= htmlspecialchars($event['title']) ?></h2>
        <p><?= htmlspecialchars($event['description']) ?></p>
        <p><?= date('d.m.Y', strtotime($event['date'])) ?> в <?= $event['time'] ?></p>
        <p><?= $event['genre'] ?></p>
        <p>Длительность: <?= $event['duration'] ?> мин</p>
        <p><?= $event['age_rating'] ?></p>
        <p>Цена билета: <?= number_format($event['price'], 0, '', ' ') ?> ₽</p>
    </div>
    
    <div class="booking-form">
        <form method="POST">
            <div class="quantity-selector">
                <label>Количество билетов:</label>
                <button type="button" onclick="changeQty(-1)">-</button>
                <span id="qtyDisplay">1</span>
                <input type="hidden" name="quantity" id="quantityInput" value="1">
                <button type="button" onclick="changeQty(1)">+</button>
            </div>
            
            <div class="price-breakdown">
                <p>Билеты: <span id="ticketsPrice"><?= number_format($event['price'], 0, '', ' ') ?></span> ₽</p>
                <p>Сервисный сбор (5%): <span id="serviceFee"><?= number_format(round($event['price'] * 0.05), 0, '', ' ') ?></span> ₽</p>
                <p class="total">Итого: <span id="totalPrice"><?= number_format(round($event['price'] * 1.05), 0, '', ' ') ?></span> ₽</p>
            </div>
            
            <button type="submit" name="add_to_cart">Добавить в корзину</button>
        </form>
    </div>
</div>

<script>
const pricePerTicket = <?= $event['price'] ?>;
let currentQty = 1;

function changeQty(delta) {
    let newQty = currentQty + delta;
    if(newQty >= 1 && newQty <= 10) {
        currentQty = newQty;
        document.getElementById('qtyDisplay').innerText = currentQty;
        document.getElementById('quantityInput').value = currentQty;
        
        let tickets = pricePerTicket * currentQty;
        let fee = Math.round(tickets * 0.05);
        let total = tickets + fee;
        
        document.getElementById('ticketsPrice').innerText = tickets.toLocaleString('ru-RU');
        document.getElementById('serviceFee').innerText = fee.toLocaleString('ru-RU');
        document.getElementById('totalPrice').innerText = total.toLocaleString('ru-RU');
    }
}
</script>

<?php include '../footer.php'; ?>