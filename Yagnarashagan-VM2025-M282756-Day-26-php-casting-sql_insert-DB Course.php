<?php


// -------------------
// MySQL configuration (change to your values)
$db_host = '127.0.0.1';
$db_name = 'testdb';
$db_user = 'root';
$db_pass = '';
// Sample SQL to create a table for this example (run in your MySQL client):
/*
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  age INT,
  score DECIMAL(6,2),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
*/

// JSON data file path (writable by the web server)
$jsonFile = __DIR__ . DIRECTORY_SEPARATOR . 'data.json';

// Simple helper to insert into JSON file
function insert_into_json($filePath, $record) {
    $data = [];
    if (file_exists($filePath)) {
        $raw = file_get_contents($filePath);
        $data = json_decode($raw, true) ?: [];
    }
    $data[] = $record;
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Casting examples ---
    // Raw input from user (strings)
    $rawName  = $_POST['name'] ?? '';
    $rawAge   = $_POST['age'] ?? '';
    $rawScore = $_POST['score'] ?? '';

    // Explicit casting to target types
    $name  = (string) trim($rawName);        // cast to string and trim whitespace
    $age   = (int) $rawAge;                 // cast to integer
    $score = (float) $rawScore;             // cast to float (decimal)

    // Basic validation
    if ($name === '') {
        $message = 'Name is required.';
    } elseif ($age < 0) {
        $message = 'Age must be 0 or positive.';
    } else {
        // Prepare the record
        $record = [
            'name' => $name,
            'age' => $age,
            'score' => $score,
            'created_at' => date('c')
        ];

        // 1) Insert into JSON file
        try {
            insert_into_json($jsonFile, $record);
            $message .= "Saved to JSON file ({$jsonFile}). ";
        } catch (Exception $e) {
            $message .= 'Failed to save JSON: ' . $e->getMessage();
        }

        // 2) Insert into MySQL using PDO with prepared statements
        try {
            $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
            $pdo = new PDO($dsn, $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            $sql = 'INSERT INTO users (name, age, score) VALUES (:name, :age, :score)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':age'  => $age,
                ':score' => $score,
            ]);

            $message .= 'Inserted into MySQL (users table).';
        } catch (PDOException $e) {
           
            $message .= ' MySQL error: ' . htmlspecialchars($e->getMessage());
        }
    }

    //types of variables after casting
    $debugTypes = [
        'name_type' => gettype($name),
        'age_type' => gettype($age),
        'score_type' => gettype($score),
    ];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Insert Example - JSON & MySQL</title>
  <style>body{font-family:Arial,Helvetica,sans-serif;max-width:800px;margin:2rem auto;padding:1rem}label{display:block;margin-top:.5rem}input[type=text],input[type=number]{width:100%;padding:.4rem}button{margin-top:.8rem;padding:.6rem 1rem}</style>
</head>
<body>
  <h1>Beginner: JSON insert, MySQL insert, Casting</h1>
  <?php if (!empty($message)): ?>
    <div style="background:#eef;padding:1rem;border-radius:6px;margin-bottom:1rem;"><?php echo nl2br(htmlspecialchars($message)); ?></div>
  <?php endif; ?>

  <form method="post">
    <label>Name
      <input type="text" name="name" required placeholder="Your name">
    </label>

    <label>Age
      <input type="number" name="age" min="0" step="1" placeholder="e.g. 21">
    </label>

    <label>Score (decimal)
      <input type="text" name="score" placeholder="e.g. 87.5">
    </label>

    <button type="submit">Save</button>
  </form>

  <?php if (!empty($debugTypes)): ?>
    <h2>Debug (types after casting)</h2>
    <ul>
      <li>Name: <?php echo htmlspecialchars($name); ?> (<?php echo $debugTypes['name_type']; ?>)</li>
      <li>Age: <?php echo htmlspecialchars((string)$age); ?> (<?php echo $debugTypes['age_type']; ?>)</li>
      <li>Score: <?php echo htmlspecialchars((string)$score); ?> (<?php echo $debugTypes['score_type']; ?>)</li>
    </ul>
  <?php endif; ?>

  <h3>Notes for assignment</h3>
  <ul>
    <li>The JSON file <code>data.json</code> will be created in the same folder. Make sure the web server user can write there.</li>
    <li>Update the MySQL credentials at the top of this file and create the <code>users</code> table using the provided SQL before testing the DB insert.</li>
    <li>Use prepared statements (as shown) to avoid SQL injection.</li>
    <li>Casting: (int), (float), (string) convert input types—useful when user input is always a string.</li>
  </ul>

</body>
</html>
