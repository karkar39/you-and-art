<?php
session_start();

$host = 'localhost';
$dbname = 'youandart';
$username = 'root';
$password = '';

try {
    // Подключаемся без БД, создаём если нет
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Создаём БД
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8 COLLATE utf8_general_ci");
    $pdo->exec("USE `$dbname`");
    
    // ==== СОЗДАЁМ ТАБЛИЦЫ ====
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        phone VARCHAR(20),
        password VARCHAR(255) NOT NULL,
        is_admin TINYINT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(200) NOT NULL,
        description TEXT,
        price INT NOT NULL,
        date DATE NOT NULL,
        time TIME,
        genre VARCHAR(50),
        image_url VARCHAR(500),
        capacity INT DEFAULT 80,
        age_rating VARCHAR(10),
        duration INT,
        is_active TINYINT DEFAULT 1
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_number VARCHAR(50) UNIQUE NOT NULL,
        user_id INT,
        event_id INT NOT NULL,
        quantity INT NOT NULL,
        total_price INT NOT NULL,
        status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (event_id) REFERENCES events(id)
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        event_id INT NOT NULL,
        quantity INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (event_id) REFERENCES events(id)
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS favorites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        event_id INT,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (event_id) REFERENCES events(id)
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        is_read TINYINT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // ==== ДОБАВЛЯЕМ АДМИНА (если нет) ====
    $check = $pdo->query("SELECT id FROM users WHERE email = 'admin@youandart.ru'");
    if(!$check->fetch()) {
        $pdo->exec("INSERT INTO users (name, email, phone, password, is_admin) VALUES 
            ('Администратор', 'admin@youandart.ru', '+79990000000', '21232f297a57a5a743894a0e4a801fc3', 1)");
    }
    
    // ==== ДОБАВЛЯЕМ МЕРОПРИЯТИЯ (если нет) ====
    $checkEvents = $pdo->query("SELECT id FROM events LIMIT 1");
    if(!$checkEvents->fetch()) {
        $pdo->exec("INSERT INTO events (title, description, price, date, time, genre, image_url, capacity, age_rating, duration, is_active) VALUES
            ('Мастер и Маргарита', 'Спектакль по роману Михаила Булгакова', 2500, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '19:00:00', 'Театр', 'https://via.placeholder.com/300x200?text=Teatr', 80, '16+', 150, 1),
            ('Русский рок', 'Концерт лучших рок-групп', 1800, DATE_ADD(CURDATE(), INTERVAL 10 DAY), '20:00:00', 'Концерт', 'https://via.placeholder.com/300x200?text=Rock', 120, '12+', 180, 1),
            ('Импрессионисты', 'Выставка картин', 800, DATE_ADD(CURDATE(), INTERVAL 14 DAY), '11:00:00', 'Выставка', 'https://via.placeholder.com/300x200?text=Art', 50, '0+', 120, 1),
            ('Ночь в театре', 'Интерактивный спектакль', 1500, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '20:00:00', 'Театр', 'https://via.placeholder.com/300x200?text=Night', 100, '12+', 90, 1),
            ('Джазовый вечер', 'Концерт джазового оркестра', 2000, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '19:30:00', 'Концерт', 'https://via.placeholder.com/300x200?text=Jazz', 80, '6+', 120, 1),
            ('Современное искусство', 'Выставка молодых художников', 500, DATE_ADD(CURDATE(), INTERVAL 8 DAY), '12:00:00', 'Выставка', 'https://via.placeholder.com/300x200?text=Art', 60, '0+', 180, 1)");
    }
    
} catch(PDOException $e) {
    die("Ошибка: " . $e->getMessage());
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

function getCartCount() {
    global $pdo;
    if(!isLoggedIn()) return 0;
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch()['total'] ?? 0;
}
?>