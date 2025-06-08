<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_klienta'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['id_klienta'];

// Pobierz wszystkie oddziały
$oddzialy = $pdo->query("
    SELECT id_oddzialu, nazwa, miasto, adres
    FROM oddzial
    ORDER BY miasto, nazwa
")->fetchAll(PDO::FETCH_ASSOC);

// Pobierz przypisany oddział/oddziały klienta
$stmt = $pdo->prepare("SELECT id_oddzialu FROM klient_oddzial WHERE id_klienta = ?");
$stmt->execute([$id]);
$przypisane = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Oddziały banku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-container">
    <h2>🏦 Oddziały banku</h2>

    <table class="tabela-przelewow">
        <thead>
            <tr>
                <th>Nazwa</th>
                <th>Miasto</th>
                <th>Adres</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($oddzialy as $o): ?>
                <tr>
                    <td><?= htmlspecialchars($o['nazwa']) ?></td>
                    <td><?= htmlspecialchars($o['miasto']) ?></td>
                    <td><?= htmlspecialchars($o['adres']) ?></td>
                    <td><?= in_array($o['id_oddzialu'], $przypisane) ? '✅ przypisany' : '—' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a class="button" href="index.php">⬅️ Powrót</a>
</div>
</body>
</html>
