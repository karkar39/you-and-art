<?php require_once '../php/db.php'; ?>

<?php
if(isset($_GET['logout'])) {
    session_destroy();
    header('Location: auth.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = md5($_POST['password']);
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if($stmt->fetch()) {
        $error = "Email уже зарегистрирован";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $password]);
        $success = "Регистрация успешна! Теперь войдите.";
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header('Location: ../index.php');
        exit;
    } else {
        $error = "Неверный email или пароль";
    }
}
?>

<?php include '../header.php'; ?>

<div class="auth-container">
    <div class="auth-tabs">
        <button class="tab-btn active" onclick="showTab('login')">🔐 Вход</button>
        <button class="tab-btn" onclick="showTab('register')">📝 Регистрация</button>
    </div>
    
    <div id="login-tab" class="auth-form">
        <h2>Вход в аккаунт</h2>
        <?php if(isset($error) && !isset($_POST['register'])): ?>
            <p class="error">❌ <?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit" name="login">Войти</button>
        </form>
        <p class="admin-hint">👑 Админ: admin@youandart.ru / admin123</p>
    </div>
    
    <div id="register-tab" class="auth-form" style="display:none">
        <h2>Регистрация</h2>
        <?php if(isset($error) && isset($_POST['register'])): ?>
            <p class="error">❌ <?= $error ?></p>
        <?php endif; ?>
        <?php if(isset($success)): ?>
            <p class="success">✅ <?= $success ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Имя" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Телефон">
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit" name="register">Зарегистрироваться</button>
        </form>
    </div>
</div>

<script>
function showTab(tab) {
    document.getElementById('login-tab').style.display = tab === 'login' ? 'block' : 'none';
    document.getElementById('register-tab').style.display = tab === 'register' ? 'block' : 'none';
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}
</script>

<?php include '../footer.php'; ?>