<?php
session_start();
require 'db.php';
if (!isset($_SESSION['klient_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['klient_id'];
$stmt = $pdo->prepare("SELECT * FROM logi WHERE id_klienta = ? ORDER BY data_akcji DESC");
$stmt->execute([$id]);
$logi = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Logi systemowe</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-container">
    <h2>🧾 Logi systemowe</h2>

    <?php if (empty($logi)): ?>
        <p>Brak logów.</p>
    <?php else: ?>
        <ul class="log-list">
            <?php foreach ($logi as $log): ?>
                <li><strong><?= $log['data_akcji'] ?></strong> — <?= htmlspecialchars($log['akcja']) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <a class="button" href="index.php">⬅️ Powrót</a>
</div>
</body>
</html>
