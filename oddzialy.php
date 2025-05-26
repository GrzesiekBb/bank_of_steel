<?php
session_start();
require 'db.php';
if (!isset($_SESSION['klient_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['klient_id'];
$oddzialy = $pdo->query("SELECT * FROM oddzial")->fetchAll();
$stmt = $pdo->prepare("SELECT id_oddzialu FROM klient_oddzial WHERE id_klienta = ?");
$stmt->execute([$id]);
$przypisane = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Oddziały banku</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-container">
    <h2>🏦 Oddziały banku</h2>

    <table class="tabela-przelewow">
        <thead>
            <tr><th>Nazwa</th><th>Miasto</th><th>Adres</th><th>Telefon</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php foreach ($oddzialy as $o): ?>
                <tr>
                    <td><?= htmlspecialchars($o['nazwa']) ?></td>
                    <td><?= htmlspecialchars($o['miasto']) ?></td>
                    <td><?= htmlspecialchars($o['adres']) ?></td>
                    <td><?= htmlspecialchars($o['telefon']) ?></td>
                    <td><?= in_array($o['id_oddzialu'], $przypisane) ? '✅ przypisany' : '—' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a class="button" href="index.php">⬅️ Powrót</a>
</div>
</body>
</html>
