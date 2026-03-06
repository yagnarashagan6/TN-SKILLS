<?php
// Simple Login System
$host = '127.0.0.1';
$port = 3306;
$db   = 'registration_db';
$user = 'root';
$pass = 'my_password';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
$message = '';
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
    $stmt->execute([':username' => $username, ':password' => $password]);
    $user = $stmt->fetch();
    if ($user) {
        $message = "Login successful!";
    } else {
        $message = "Invalid credentials.";
    }
}
?>
<!doctype html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial; background: #f4f6fb; }
        .container { max-width: 400px; margin: 60px auto; background: #fff; padding: 32px; border-radius: 10px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        label { display: block; margin-top: 16px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; margin-top: 6px; border: 1px solid #cfd8dc; border-radius: 4px; }
        button { background: #3b82f6; color: #fff; border: none; border-radius: 4px; padding: 10px 22px; font-size: 1rem; margin-top: 18px; cursor: pointer; }
        .message { text-align: center; margin-bottom: 18px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if ($message !== ''): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="post">
            <label>Username
                <input type="text" name="username" required>
            </label>
            <label>Password
                <input type="password" name="password" required>
            </label>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
