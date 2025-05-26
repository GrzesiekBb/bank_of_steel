<?php
session_start();
require 'db.php';
require 'klasy.php';
if (!isset($_SESSION['klient_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['klient_id'];
$stmt = $pdo->prepare("SELECT * FROM klient WHERE id_klienta = ?");
$stmt->execute([$id]);
$klient_stk = $stmt->fetch();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noweHaslo = $_POST['nowe_haslo'] ?? '';
    $potwierdzHaslo = $_POST['potwierdz_haslo'] ?? '';

    $klient = new Klient();
    $klient->id = $_SESSION['klient_id'];
    $komunikat = $klient->zmienHasloBezpiecznie($noweHaslo, $potwierdzHaslo, $pdo);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Ustawienia konta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-container">
    <h2>⚙️ Ustawienia konta</h2>
    <div class="konto-box">
        <p><strong>Imię:</strong> <?= htmlspecialchars($klient_stk['imie']) ?></p>
        <p><strong>Nazwisko:</strong> <?= htmlspecialchars($klient_stk['nazwisko']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($klient_stk['email']) ?></p>
        <p><strong>Telefon:</strong> <?= htmlspecialchars($klient_stk['telefon']) ?></p>
        <p><strong>Adres:</strong> <?= htmlspecialchars($klient_stk['adres']) ?></p>
    </div>
    <form method="post" class="pswdchng-form">
            <label for="nowe_haslo">Nowe hasło:</label>
            <input type="password" name="nowe_haslo" id="nowe_haslo" required>

            <label for="potwierdz_haslo">Potwierdź nowe hasło:</label>
            <input type="password" name="potwierdz_haslo" id="potwierdz_haslo" required>

            <button type="submit">Zmień hasło</button>
    </form>
    <a class="button" href="index.php">⬅️ Powrót</a>
</div>
</body>
</html>
