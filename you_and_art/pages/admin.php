<?php require_once '../php/db.php'; ?>

<?php
if(!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_event'])) {
    $stmt = $pdo->prepare("INSERT INTO events (title, description, price, date, time, genre, image_url, capacity, age_rating, duration) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['title'], $_POST['description'], $_POST['price'], 
        $_POST['date'], $_POST['time'], $_POST['genre'], 
        $_POST['image_url'], $_POST['capacity'], $_POST['age_rating'], $_POST['duration']
    ]);
    $success = "Мероприятие добавлено!";
}

if(isset($_GET['delete_event'])) {
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$_GET['delete_event']]);
    header('Location: admin.php');
    exit;
}

$stats = [];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM events WHERE date >= CURDATE()");
$stats['events'] = $stmt->fetch()['total'];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings");
$stats['orders'] = $stmt->fetch()['total'];
$stmt = $pdo->query("SELECT SUM(total_price) as revenue FROM bookings WHERE status = 'paid'");
$stats['revenue'] = $stmt->fetch()['revenue'] ?? 0;
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$stats['users'] = $stmt->fetch()['total'];

$events = $pdo->query("SELECT * FROM events ORDER BY date DESC")->fetchAll();
?>

<?php include '../header.php'; ?>

<div class="admin-container">
    <h2>⚙️ Админ-панель</h2>
    
    <div class="stats-grid">
        <div class="stat-card">📅 Мероприятий: <?= $stats['events'] ?></div>
        <div class="stat-card">📦 Заказов: <?= $stats['orders'] ?></div>
        <div class="stat-card">💰 Выручка: <?= number_format($stats['revenue'], 0, '', ' ') ?> ₽</div>
        <div class="stat-card">👥 Пользователей: <?= $stats['users'] ?></div>
    </div>
    
    <div class="add-event-form">
        <h3>➕ Добавить мероприятие</h3>
        <?php if(isset($success)): ?>
            <p class="success">✅ <?= $success ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="title" placeholder="Название" required>
            <textarea name="description" placeholder="Описание" rows="3"></textarea>
            <input type="number" name="price" placeholder="Цена" required>
            <input type="date" name="date" required>
            <input type="time" name="time" required>
            <select name="genre">
                <option value="Театр">🎭 Театр</option>
                <option value="Концерт">🎵 Концерт</option>
                <option value="Выставка">🎨 Выставка</option>
                <option value="Кино">🎬 Кино</option>
            </select>
            <input type="text" name="image_url" placeholder="URL картинки" value="https://via.placeholder.com/300x200">
            <input type="number" name="capacity" placeholder="Вместимость" value="80">
            <input type="text" name="age_rating" placeholder="Возрастной рейтинг" value="12+">
            <input type="number" name="duration" placeholder="Длительность (мин)" value="120">
            <button type="submit" name="add_event">➕ Добавить</button>
        </form>
    </div>
    
    <div class="events-list">
        <h3>📋 Список мероприятий</h3>
        <table>
            <thead>
                <tr><th>Название</th><th>Дата</th><th>Цена</th><th>Действия</th></tr>
            </thead>
            <tbody>
                <?php foreach($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['title']) ?></td>
                    <td><?= date('d.m.Y', strtotime($event['date'])) ?></td>
                    <td><?= number_format($event['price'], 0, '', ' ') ?> ₽</td>
                    <td><a href="admin.php?delete_event=<?= $event['id'] ?>" onclick="return confirm('Удалить мероприятие?')" style="color:#e74c3c;">🗑️ Удалить</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../footer.php'; ?>