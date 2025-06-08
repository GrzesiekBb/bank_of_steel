<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_klienta'])) {
    header('Location: login.php');
    exit;
}

$id_klienta = $_SESSION['id_klienta'];

// Pobierz wszystkie kredyty klienta
$stmt = $pdo->prepare("
    SELECT k.id_kredytu, k.kwota, k.liczba_rat, k.oprocentowanie, k.data_udzielenia
    FROM kredyt k
    JOIN kredyt_klient kk ON kk.id_kredyt = k.id_kredytu
    WHERE kk.id_klienta = :id
");
$stmt->execute(['id' => $id_klienta]);
$kredyty = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pobierz wszystkie raty powiązane z kredytami klienta
$stmt = $pdo->prepare("
    SELECT rkk.id_kredytu, rk.numer_raty, rk.kwota_raty, rk.data_splaty, rk.status_splaty
    FROM rata_kredytowa rk
    JOIN rata_kredytowa_kredyt rkk ON rkk.id_raty = rk.id_raty
    JOIN kredyt_klient kk ON kk.id_kredyt = rkk.id_kredytu
    WHERE kk.id_klienta = :id
    ORDER BY rkk.id_kredytu, rk.numer_raty
");
$stmt->execute(['id' => $id_klienta]);
$raty = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Grupuj raty po kredytach
$raty_kredyty = [];
foreach ($raty as $rata) {
    $raty_kredyty[$rata['id_kredytu']][] = $rata;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>🧾 Twoje kredyty i raty</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-container">
    <h2>💳 Twoje kredyty</h2>

    <?php if (empty($kredyty)): ?>
        <p>Nie posiadasz aktywnych kredytów.</p>
    <?php else: ?>
        <?php foreach ($kredyty as $kredyt): ?>
            <div class="konto-box">
                <p><strong>ID kredytu:</strong> <?= $kredyt['id_kredytu'] ?></p>
                <p><strong>Kwota:</strong> <?= number_format($kredyt['kwota'], 2) ?> PLN</p>
                <p><strong>Liczba rat:</strong> <?= $kredyt['liczba_rat'] ?></p>
                <p><strong>Oprocentowanie:</strong> <?= $kredyt['oprocentowanie'] ?>%</p>
                <p><strong>Data udzielenia:</strong> <?= $kredyt['data_udzielenia'] ?></p>
            </div>

            <h4>📆 Raty kredytu #<?= $kredyt['id_kredytu'] ?></h4>
            <table class="tabela-przelewow">
                <thead>
                    <tr>
                        <th>Numer raty</th>
                        <th>Kwota</th>
                        <th>Data spłaty</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($raty_kredyty[$kredyt['id_kredytu']] ?? [] as $rata): ?>
                        <tr>
                            <td><?= $rata['numer_raty'] ?></td>
                            <td><?= number_format($rata['kwota_raty'], 2) ?> PLN</td>
                            <td><?= $rata['data_splaty'] ?></td>
                            <td><?= htmlspecialchars($rata['status_splaty']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>

    <a class="button" href="index.php">⬅️ Powrót</a>
</div>
</body>
</html>
