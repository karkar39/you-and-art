<?php require_once '../php/db.php'; ?>
<?php include '../header.php'; ?>

<div class="filters">
    <form method="GET" action="" class="filter-form">
        <input type="text" name="search" placeholder="🔍 Поиск..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <select name="genre">
            <option value="all">🎭 Все жанры</option>
            <?php
            $genres = $pdo->query("SELECT DISTINCT genre FROM events WHERE genre IS NOT NULL");
            while($g = $genres->fetch()):
            ?>
            <option value="<?= $g['genre'] ?>" <?= ($_GET['genre'] ?? '') == $g['genre'] ? 'selected' : '' ?>><?= $g['genre'] ?></option>
            <?php endwhile; ?>
        </select>
        <select name="price">
            <option value="all">💰 Любая цена</option>
            <option value="0-1000" <?= ($_GET['price'] ?? '') == '0-1000' ? 'selected' : '' ?>>до 1000 ₽</option>
            <option value="1000-2000" <?= ($_GET['price'] ?? '') == '1000-2000' ? 'selected' : '' ?>>1000-2000 ₽</option>
            <option value="2000-999999" <?= ($_GET['price'] ?? '') == '2000-999999' ? 'selected' : '' ?>>от 2000 ₽</option>
        </select>
        <button type="submit">Применить</button>
    </form>
</div>

<div class="events-grid">
    <?php
    $sql = "SELECT * FROM events WHERE date >= CURDATE() AND is_active = 1";
    $params = [];
    
    if(!empty($_GET['search'])) {
        $sql .= " AND (title LIKE ? OR description LIKE ?)";
        $search = "%{$_GET['search']}%";
        $params[] = $search;
        $params[] = $search;
    }
    
    if(!empty($_GET['genre']) && $_GET['genre'] != 'all') {
        $sql .= " AND genre = ?";
        $params[] = $_GET['genre'];
    }
    
    if(!empty($_GET['price']) && $_GET['price'] != 'all') {
        $parts = explode('-', $_GET['price']);
        $min = $parts[0];
        $max = $parts[1];
        $sql .= " AND price BETWEEN ? AND ?";
        $params[] = $min;
        $params[] = $max;
    }
    
    $sql .= " ORDER BY date ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    if($stmt->rowCount() == 0) {
        echo "<p style='grid-column:1/-1; text-align:center;'>😔 Мероприятий не найдено</p>";
    }
    
    while($event = $stmt->fetch(PDO::FETCH_ASSOC)):
    ?>
    <div class="event-card">
        <img src="<?= $event['image_url'] ?>" alt="<?= $event['title'] ?>">
        <div class="event-info">
            <h3><?= htmlspecialchars($event['title']) ?></h3>
            <p>📅 <?= date('d.m.Y', strtotime($event['date'])) ?> в <?= $event['time'] ?></p>
            <p>🎭 <?= $event['genre'] ?></p>
            <p class="event-price"><?= number_format($event['price'], 0, '', ' ') ?> ₽</p>
            <a href="booking.php?id=<?= $event['id'] ?>"><button>Купить билет</button></a>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php include '../footer.php'; ?>