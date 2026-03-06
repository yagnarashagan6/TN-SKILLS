<?php
// DB config
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

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Create table if not exists
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS registrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            age INT NOT NULL,
            food_pref ENUM('veg','non-veg') NOT NULL,
            phone VARCHAR(20) NOT NULL,
            photo_path VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $name = trim($_POST['name'] ?? '');
        $age = (int)($_POST['age'] ?? 0);
        $food = $_POST['food_pref'] ?? '';
        $phone = trim($_POST['phone'] ?? '');

        if ($name === '' || $age <= 0 || !in_array($food, ['veg', 'non-veg'], true) || $phone === '') {
            $message = "Please fill all fields correctly.";
        } elseif (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $message = "Please upload a valid photo.";
        } else {
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES['photo']['tmp_name']);

            if (!isset($allowed[$mime])) {
                $message = "Only JPG, PNG, WEBP images are allowed.";
            } else {
                $uploadDir = __DIR__ . '/uploads';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = 'photo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
                $targetPath = $uploadDir . '/' . $fileName;
                $dbPhotoPath = 'uploads/' . $fileName;

                if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                    $stmt = $pdo->prepare("
                        INSERT INTO registrations (name, age, food_pref, phone, photo_path)
                        VALUES (:name, :age, :food, :phone, :photo)
                    ");
                    $stmt->execute([
                        ':name' => $name,
                        ':age' => $age,
                        ':food' => $food,
                        ':phone' => $phone,
                        ':photo' => $dbPhotoPath
                    ]);
                    $message = "Registered successfully.";
                } else {
                    $message = "Photo upload failed.";
                }
            }
        }
    }

    if ($action === 'mask_phone') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            // UPDATE with WHERE
            $stmt = $pdo->prepare("UPDATE registrations SET phone = 'xxxxx' WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $message = $stmt->rowCount() ? "Phone changed to xxxxx for ID $id." : "ID not found.";
        }
    }

    if ($action === 'delete_row') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            // Optionally, delete the photo file as well
            $stmt = $pdo->prepare("SELECT photo_path FROM registrations WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            if ($row && isset($row['photo_path'])) {
                $photoFile = __DIR__ . '/' . $row['photo_path'];
                if (file_exists($photoFile)) {
                    @unlink($photoFile);
                }
            }
            $stmt = $pdo->prepare("DELETE FROM registrations WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $message = $stmt->rowCount() ? "Row deleted successfully." : "ID not found.";
        }
    }
}

$rows = $pdo->query("SELECT * FROM registrations ORDER BY id DESC")->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registration</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6fb;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 24px 24px 24px;
        }
        h2, h3 {
            text-align: center;
            color: #2d3a4b;
        }
        form {
            background: #f9fafc;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            padding: 24px 20px 16px 20px;
            margin: 0 auto 32px auto;
            max-width: 420px;
            border: none;
        }
        label {
            display: block;
            margin-top: 16px;
            color: #34495e;
            font-weight: 500;
        }
        input[type="text"],
        input[type="number"],
        input[type="file"] {
            width: 100%;
            padding: 8px 10px;
            margin-top: 6px;
            border: 1px solid #cfd8dc;
            border-radius: 4px;
            font-size: 1rem;
            background: #fff;
            box-sizing: border-box;
        }
        input[type="radio"] {
            margin-right: 6px;
        }
        button[type="submit"], button {
            background: #3b82f6;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 22px;
            font-size: 1rem;
            font-weight: 600;
            margin-top: 18px;
            cursor: pointer;
            transition: background 0.2s;
        }
        button[type="submit"]:hover, button:hover {
            background: #2563eb;
        }
        .message {
            text-align: center;
            margin-bottom: 18px;
            padding: 10px 0;
            border-radius: 6px;
            font-weight: 500;
            font-size: 1.08rem;
        }
        .message.success {
            background: #e0f7e9;
            color: #15803d;
            border: 1px solid #34d399;
        }
        .message.error {
            background: #fbeaea;
            color: #b91c1c;
            border: 1px solid #f87171;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 24px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        th, td {
            border: none;
            padding: 12px 10px;
            text-align: left;
        }
        th {
            background: #f1f5f9;
            color: #374151;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }
        @media (max-width: 700px) {
            .container { padding: 10px; }
            form { max-width: 100%; padding: 12px; }
            table, thead, tbody, th, td, tr { display: block; }
            th, td { padding: 8px 4px; }
            tr { margin-bottom: 16px; border-bottom: 1px solid #e5e7eb; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registration Form</h2>
        <?php if ($message !== ''): ?>
            <div class="message <?= strpos($message, 'success') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="register">

            <label>Name
                <input type="text" name="name" required>
            </label>

            <label>Age
                <input type="number" name="age" min="1" required>
            </label>

            <label>Food Preference</label>
            <label style="display:inline; margin-right:16px;"><input type="radio" name="food_pref" value="veg" required> Veg</label>
            <label style="display:inline;"><input type="radio" name="food_pref" value="non-veg" required> Non Veg</label>

            <label>Phone Number
                <input type="text" name="phone" required>
            </label>

            <label>Photo
                <input type="file" name="photo" accept="image/*" required>
            </label>

            <button type="submit">Register</button>
        </form>

        <h3>Registered Data</h3>
        <div style="overflow-x:auto;">
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Age</th>
                <th>Food</th>
                <th>Phone</th>
                <th>Photo</th>
                <th>Created</th>
                <th>Action</th>
            </tr>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= (int)$r['age'] ?></td>
                    <td><?= htmlspecialchars($r['food_pref']) ?></td>
                    <td><?= htmlspecialchars($r['phone']) ?></td>
                    <td><img src="<?= htmlspecialchars($r['photo_path']) ?>" alt="photo"></td>
                    <td><?= htmlspecialchars($r['created_at']) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="action" value="mask_phone">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <button type="submit">Set Phone = xxxxx</button>
                        </form>
                        <form method="post" style="display:inline; margin-left:6px;">
                            <input type="hidden" name="action" value="delete_row">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this row?');" style="background:#ef4444;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        </div>
    </div>
</body>
</html>
