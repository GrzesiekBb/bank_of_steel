<?php
session_start();
require_once 'db.php';        // zakłada, że masz zmienną $pdo
require_once 'klasy.php';     // lub 'Klient.php', gdzie jest klasa Klient

$blad = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $haslo = $_POST['haslo'] ?? '';

    $klient = Klient::zaloguj($login, $haslo, $pdo);

    if ($klient !== null) {
        $_SESSION['klient_id'] = $klient->id;
        $_SESSION['klient_imie'] = $klient->imie;
        header("Location: index.php");
        exit;
    } else {
        $blad = "Nieprawidłowy login lub hasło.";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">

    <!-- Logo nad wszystkim -->
    <div class="login-logo">
        <img src="logo1.png" alt="Logo banku">
    </div>

    <!-- Kontener formularza -->
    <div class="login-container">
        <h2>Logowanie</h2>
        <?php if (!empty($blad)) echo "<div class='error'>$blad</div>"; ?>
        <form method="post" class="login-form">
            <label for="login">Login:</label>
            <input type="text" name="login" id="login" required>

            <label for="haslo">Hasło:</label>
            <input type="password" name="haslo" id="haslo" required>

            <button type="submit">Zaloguj</button>
        </form>
    </div>

</body>
</html>
