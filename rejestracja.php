<?php
session_start();
require_once 'db.php';

$komunikat = '';
$oddzialy = [];

try {
    $stmt = $pdo->query("SELECT ID_oddzialu, Nazwa, Miasto FROM Oddzial");
    $oddzialy = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $komunikat = "Błąd podczas ładowania oddziałów: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imie = $_POST['imie'] ?? '';
    $nazwisko = $_POST['nazwisko'] ?? '';
    $pesel = $_POST['pesel'] ?? '';
    $adres = $_POST['adres'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefon = $_POST['telefon'] ?? '';
    $id_oddzialu = $_POST['oddzial'] ?? '';
    $login = $_POST['login'] ?? '';
    $haslo = $_POST['haslo'] ?? '';

    if (empty($imie) || empty($nazwisko) || empty($pesel) || empty($adres) || empty($email)
        || empty($telefon) || empty($id_oddzialu) || empty($login) || empty($haslo)) {
        $komunikat ="blad";
        
    } else {
        try {
            $stmt = $pdo->prepare("SELECT rejestracja_klienta_fun(:imie, :nazwisko, :pesel, :adres, :email, :telefon, :oddzial, :login, :haslo)");
            $stmt->execute([
                ':imie' => $imie,
                ':nazwisko' => $nazwisko,
                ':pesel' => $pesel,
                ':adres' => $adres,
                ':email' => $email,
                ':telefon' => $telefon,
                ':oddzial' => $id_oddzialu,
                ':login' => $login,
                ':haslo' => password_hash($haslo, PASSWORD_DEFAULT)
            ]);
            $rezultat = $stmt->fetchColumn();
            $komunikat = $rezultat;

            if (str_starts_with($rezultat, "Rejestracja")) {
                header("Location: login.php");
                exit;
            }
        } catch (PDOException $e) {
            $komunikat = "Błąd podczas rejestracji: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
    <div class="login-logo">
        <img src="logo1.png" alt="Logo banku">
    </div>
    <div class="login-container">
        <h2>Rejestracja konta</h2>
        <?php if (!empty($komunikat)): ?>
            <div class="error"><?= htmlspecialchars($komunikat) ?></div>
        <?php endif; ?>
        <form method="post" class="login-form">
            <label for="imie">Imię:</label>
            <input type="text" name="imie" id="imie" required>

            <label for="nazwisko">Nazwisko:</label>
            <input type="text" name="nazwisko" id="nazwisko" required>

            <label for="pesel">PESEL:</label>
            <input type="text" name="pesel" id="pesel" required>

            <label for="adres">Adres:</label>
            <input type="text" name="adres" id="adres" required>

            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" required>

            <label for="telefon">Telefon:</label>
            <input type="text" name="telefon" id="telefon" required>

            <label for="oddzial">Oddział:</label>
            <select name="oddzial" id="oddzial" required>
                <option value="">-- Wybierz oddział --</option>
                <?php foreach ($oddzialy as $oddzial): ?>
                    <option value="<?= htmlspecialchars($oddzial['id_oddzialu'] ?? '') ?>">
                        <?= htmlspecialchars($oddzial['nazwa']) ?> — <?= htmlspecialchars($oddzial['miasto']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="login">Login:</label>
            <input type="text" name="login" id="login" required>

            <label for="haslo">Hasło:</label>
            <input type="password" name="haslo" id="haslo" required>

            <button type="submit">Zarejestruj się</button>
        </form>
        <a href="login.php" class="button">Powrót do logowania</a>
    </div>
</body>
</html>
