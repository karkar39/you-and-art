<?php require_once '../php/db.php'; ?>

<?php
if(!isLoggedIn()) {
    header('Location: auth.php');
    exit;
}

if(isset($_GET['remove'])) {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['remove'], $_SESSION['user_id']]);
    header('Location: cart.php');
    exit;
}

if(isset($_POST['checkout'])) {
    $stmt = $pdo->prepare("SELECT c.*, e.price FROM cart c JOIN events e ON c.event_id = e.id WHERE c.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cartItems = $stmt->fetchAll();
    
    foreach($cartItems as $item) {
        $orderNumber = 'ORD' . time() . rand(100, 999);
        $totalPrice = $item['price'] * $item['quantity'];
        $stmt2 = $pdo->prepare("INSERT INTO bookings (order_number, user_id, event_id, quantity, total_price, status) VALUES (?, ?, ?, ?, ?, 'paid')");
        $stmt2->execute([$orderNumber, $_SESSION['user_id'], $item['event_id'], $item['quantity'], $totalPrice]);
    }
    
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    header('Location: profile.php');
    exit;
}

$stmt = $pdo->prepare("SELECT c.id as cart_id, c.quantity, e.* FROM cart c JOIN events e ON c.event_id = e.id WHERE c.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cartItems = $stmt->fetchAll();
$total = 0;
?>

<?php include '../header.php'; ?>

<div class="cart-container">
    <h2>Моя корзина</h2>
    
    <?php if(empty($cartItems)): ?>
        <div class="empty-cart">
            <p>Корзина пуста</p>
            <a href="events.php"><button>Перейти к афише</button></a>
        </div>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach($cartItems as $item): 
                $itemTotal = $item['price'] * $item['quantity'];
                $total += $itemTotal;
            ?>
            <div class="cart-item">
                <div class="cart-item-info">
                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                    <p><?= date('d.m.Y', strtotime($item['date'])) ?> в <?= $item['time'] ?></p>
                    <p><?= number_format($item['price'], 0, '', ' ') ?> ₽ × <?= $item['quantity'] ?></p>
                    <p class="item-total">= <?= number_format($itemTotal, 0, '', ' ') ?> ₽</p>
                </div>
                <a href="cart.php?remove=<?= $item['cart_id'] ?>" class="remove-link" onclick="return confirm('Удалить?')">Удалить</a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="cart-total">
            <h3>Итого: <?= number_format($total, 0, '', ' ') ?> ₽</h3>
            <form method="POST">
                <button type="submit" name="checkout">Оформить заказ</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php include '../footer.php'; ?>