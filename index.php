<?php

$host = 'db';
$dbname = 'mariadb';
$user = 'mariadb';
$pass = 'mariadb';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );
} catch (PDOException $e) {
    die('Błąd połączenia z bazą danych.');
}

$pdo->exec("CREATE TABLE IF NOT EXISTS notatki (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tresc VARCHAR(255) NOT NULL,
    utworzono TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['tresc'])) {
    $stmt = $pdo->prepare("INSERT INTO notatki (tresc) VALUES (:tresc)");
    $stmt->execute([
        ':tresc' => $_POST['tresc']
    ]);

    header('Location: /');
    exit;
}

$notatki = $pdo->query(
    "SELECT id, tresc, utworzono FROM notatki ORDER BY id DESC"
)->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Moje notatki</title>
</head>
<body>

    <h1>Moje notatki</h1>

    <form method="post">
        <input name="tresc" placeholder="Wpisz notatkę" required>
        <button type="submit">Zapisz</button>
    </form>

    <ul>
        <?php foreach ($notatki as $n): ?>
            <li>
                <?= htmlspecialchars($n['tresc'], ENT_QUOTES, 'UTF-8') ?>
                <small>(<?= htmlspecialchars($n['utworzono'], ENT_QUOTES, 'UTF-8') ?>)</small>
            </li>
        <?php endforeach; ?>
    </ul>

</body>
</html>
