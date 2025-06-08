<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_klienta'])) {
    header('Location: login.php');
    exit;
}

$id_klienta = $_SESSION['id_klienta'];

// Pobierz logi powiązane z klientem
$stmt = $pdo->prepare("
    SELECT l.czas, l.typ_zdarzenia, l.szczegoly
    FROM log_systemowy l
    JOIN log_klient lk ON lk.id_loga = l.id_loga
    WHERE lk.id_klienta = :id
    ORDER BY l.czas DESC
");
$stmt->execute(['id' => $id_klienta]);
$logi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>🧾 Logi systemowe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-container">
    <h2>🧾 Logi systemowe</h2>

    <?php if (empty($logi)): ?>
        <p>Brak logów powiązanych z Twoim kontem.</p>
    <?php else: ?>
        <ul class="log-list">
            <?php foreach ($logi as $log): ?>
                <li>
                    <strong><?= $log['czas'] ?></strong> — 
                    <em><?= htmlspecialchars($log['typ_zdarzenia']) ?>:</em> 
                    <?= htmlspecialchars($log['szczegoly']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <a class="button" href="index.php">⬅️ Powrót</a>
</div>
</body>
</html>
