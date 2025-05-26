<?php
require 'start.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"><title>Panel klienta</title>
    <link rel="stylesheet" href="style.css">
    <script>
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</head>
<body>
<div class="header">
    <div class="header-left">
        <h2>👋 Witaj, <?= htmlspecialchars($_SESSION['klient_imie']) ?>!</h2>
    </div>

    <div class="header-center">
        <img src="logo1.png" alt="Logo banku" class="logo-img">
    </div>

    <div class="header-right">
        <div class="select-wrapper">
            <select onchange="location = this.value;">
                <option value="">⬇️ Menu</option>
                <option value="ustawienia.php">⚙️ Ustawienia konta</option>
                <option value="przelewy.php">💸 Przelewy</option>
                <option value="logi.php">🧾 Logi systemowe</option>
                <option value="oddzialy.php">🏦 Oddziały banku</option>
                <option value="logout.php">🚪 Wyloguj się</option>
            </select>
        </div>
    </div>
</div>
    <h3>Twoje konta</h3>
    <?php foreach ($konta as $konto): ?>
        <div class="konto-box">
            <p><strong>Typ konta:</strong> <?= htmlspecialchars($konto['typ_konta']) ?></p>
            <p><strong>Numer konta:</strong> <?= htmlspecialchars($konto['numer_konta']) ?></p>
            <p><strong>Saldo:</strong> <?= $konto['saldo'] ?> PLN</p>
            <p><strong>Status:</strong> <?= htmlspecialchars($konto['status']) ?></p>
        </div>
    <?php endforeach; ?>



    
</body>
</html>
