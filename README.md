# You and Art — Культурный центр

Информационная система для культурного центра, организующего кино-, театральные и шоу-мероприятия, выставки и другие культурные программы.

---

## О проекте

Проект представляет собой веб-приложение для:
- Просмотра афиши мероприятий
- Поиска и фильтрации событий
- Бронирования билетов
- Личного кабинета пользователя
- Административной панели для управления мероприятиями

---

## Технологии

- **Backend:** PHP 7.4+
- **Frontend:** HTML5, CSS3, JavaScript
- **База данных:** MySQL (phpMyAdmin)
- **Сервер:** OpenServer / XAMPP

---

## Установка и запуск

### Требования
- OpenServer или XAMPP
- PHP 7.4 или выше
- MySQL

ИНСТРУКЦИЯ ПО ЗАПУСКУ:

1. Установи OpenServer/XAMPP
2. Запусти Apache и MySQL
3. Зайди в phpMyAdmin: http://localhost/phpmyadmin
4. Создай базу данных с именем youandart
5. Импортируй файл database.sql (вкладка Импорт)
6. Папку you-and-art скопируй в C:\OSPanel\domains\ (или htdocs)
7. Открой http://localhost/you-and-art/index.php

Логин админа: admin@youandart.ru
Пароль: admin123

код для бд в phpmyadmin:
-- Таблица пользователей
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица мероприятий
CREATE TABLE IF NOT EXISTS events (
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
);

-- Таблица бронирований
CREATE TABLE IF NOT EXISTS bookings (
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
);

-- Таблица корзины
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);

-- Таблица избранного
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    event_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);

-- Таблица обратной связи
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Добавляем админа
INSERT INTO users (name, email, phone, password, is_admin) VALUES 
('Администратор', 'admin@youandart.ru', '79990000000', '21232f297a57a5a743894a0e4a801fc3', 1);

-- Добавляем тестовые мероприятия
INSERT INTO events (title, description, price, date, time, genre, image_url, capacity, age_rating, duration, is_active) VALUES
('Мастер и Маргарита', 'Спектакль по роману Михаила Булгакова', 2500, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '19:00:00', 'Театр', 'https://via.placeholder.com/300x200?text=Teatr', 80, '16+', 150, 1),
('Русский рок', 'Концерт лучших рок-групп', 1800, DATE_ADD(CURDATE(), INTERVAL 10 DAY), '20:00:00', 'Концерт', 'https://via.placeholder.com/300x200?text=Rock', 120, '12+', 180, 1),
('Импрессионисты', 'Выставка картин', 800, DATE_ADD(CURDATE(), INTERVAL 14 DAY), '11:00:00', 'Выставка', 'https://via.placeholder.com/300x200?text=Art', 50, '0+', 120, 1);
