<?php include 'header.php'; ?>

<section class="hero">
    <h1>Ты и искусство</h1>
    <p>Кино, театр, выставки и шоу-программы — всё в одном месте</p>
    <a href="pages/events.php"><button>🎫 Купить билет</button></a>
</section>

<section class="events-section">
    <h2>🌟 Ближайшие мероприятия</h2>
    <div class="events-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM events WHERE date >= CURDATE() AND is_active = 1 ORDER BY date ASC LIMIT 4");
        while($event = $stmt->fetch(PDO::FETCH_ASSOC)):
        ?>
        <div class="event-card">
            <img src="<?= $event['image_url'] ?>" alt="<?= $event['title'] ?>">
            <div class="event-info">
                <h3><?= htmlspecialchars($event['title']) ?></h3>
                <p>📅 <?= date('d.m.Y', strtotime($event['date'])) ?></p>
                <p>🎭 <?= $event['genre'] ?></p>
                <p class="event-price"><?= number_format($event['price'], 0, '', ' ') ?> ₽</p>
                <a href="pages/booking.php?id=<?= $event['id'] ?>"><button>Купить билет</button></a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include 'footer.php'; ?>