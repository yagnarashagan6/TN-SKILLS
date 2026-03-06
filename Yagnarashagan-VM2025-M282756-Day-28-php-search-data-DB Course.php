<?php
// Search Concept
$host = '127.0.0.1';
$port = 3306;
$db   = 'registration_db';
$user = 'root';
$pass = 'Rashagan6@';
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
$results = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search = trim($_POST['search'] ?? '');
    if ($search !== '') {
        $stmt = $pdo->prepare("SELECT * FROM registrations WHERE name LIKE :search");
        $stmt->execute([':search' => "%$search%"]);
        $results = $stmt->fetchAll();
        $message = count($results) ? "Results found:" : "No results found.";
    } else {
        $message = "Please enter a search term.";
    }
}
?>
<!doctype html>
<html>
<head>
    <title>Search Registrations</title>
    <style>
        body { font-family: Arial; background: #f4f6fb; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; padding: 32px; border-radius: 10px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        label { display: block; margin-top: 16px; }
        input[type="text"] { width: 100%; padding: 8px; margin-top: 6px; border: 1px solid #cfd8dc; border-radius: 4px; }
        button { background: #3b82f6; color: #fff; border: none; border-radius: 4px; padding: 10px 22px; font-size: 1rem; margin-top: 18px; cursor: pointer; }
        .message { text-align: center; margin-bottom: 18px; }
        table { border-collapse: collapse; width: 100%; margin-top: 24px; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.03); }
        th, td { border: none; padding: 12px 10px; text-align: left; }
        th { background: #f1f5f9; color: #374151; font-weight: 600; }
        tr:nth-child(even) { background: #f9fafb; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Search Registrations</h2>
        <?php if ($message !== ''): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="post">
            <label>Search by Name
                <input type="text" name="search" required>
            </label>
            <button type="submit">Search</button>
        </form>
        <?php if (count($results)): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Food</th>
                    <th>Phone</th>
                </tr>
                <?php foreach ($results as $r): ?>
                    <tr>
                        <td><?= (int)$r['id'] ?></td>
                        <td><?= htmlspecialchars($r['name']) ?></td>
                        <td><?= (int)$r['age'] ?></td>
                        <td><?= htmlspecialchars($r['food_pref']) ?></td>
                        <td><?= htmlspecialchars($r['phone']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
