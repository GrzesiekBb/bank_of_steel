<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_klienta'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['id_klienta'];

// Pobranie danych klienta
$stmt = $pdo->prepare("SELECT * FROM klient WHERE id_klienta = ?");
$stmt->execute([$id]);
$klient_stk = $stmt->fetch();

// Obsługa zmiany hasła
$komunikat_haslo = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zmien_haslo'])) {
    $nowe = $_POST['nowe_haslo'] ?? '';
    $potwierdz = $_POST['potwierdz_haslo'] ?? '';

    if (strlen($nowe) < 6) {
        $komunikat_haslo = "Hasło musi mieć przynajmniej 6 znaków.";
    } elseif ($nowe !== $potwierdz) {
        $komunikat_haslo = "Hasła się nie zgadzają.";
    } else {
        try {
            $zahaszowane = password_hash($nowe, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("CALL zmien_haslo(:id, :haslo)");
            $stmt->execute([
                'id' => $id,
                'haslo' => $zahaszowane
            ]);
            $komunikat_haslo = "Hasło zostało zmienione.";
        } catch (PDOException $e) {
            $komunikat_haslo = "Błąd zmiany hasła: " . $e->getMessage();
        }
    }
}

// Obsługa tworzenia nowego rachunku
$komunikat_rachunek = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otworz_rachunek'])) {
    $rodzaj = $_POST['rodzaj_rachunku'] ?? '';

    if (in_array($rodzaj, ['Oszczednosciowy', 'Rozliczeniowy', 'Walutowy', 'Lokata'])) {
        try {
            $stmt = $pdo->prepare("CALL otworz_rachunek_proc(:id, :rodzaj)");
            $stmt->execute([
                'id' => $id,
                'rodzaj' => $rodzaj
            ]);
            $komunikat_rachunek = "Rachunek typu '$rodzaj' został pomyślnie otwarty.";
        } catch (PDOException $e) {
            $komunikat_rachunek = "Błąd przy otwieraniu rachunku: " . $e->getMessage();
        }
    } else {
        $komunikat_rachunek = "Nieprawidłowy typ rachunku.";
    }
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

    <a class="button" href="index.php">⬅️ Powrót</a>
</div>

<div class="page-container">
    <h3>🔐 Zmień hasło</h3>
    <form method="post" class="pswdchng-form">
        <label for="nowe_haslo">Nowe hasło:</label>
        <input type="password" name="nowe_haslo" id="nowe_haslo" required>

        <label for="potwierdz_haslo">Potwierdź nowe hasło:</label>
        <input type="password" name="potwierdz_haslo" id="potwierdz_haslo" required>

        <button type="submit" name="zmien_haslo">Zmień hasło</button>
    </form>

    <?php if (!empty($komunikat_haslo)): ?>
        <p class="info"><?= htmlspecialchars($komunikat_haslo) ?></p>
    <?php endif; ?>
</div>

<div class="page-container">
    <h3>🔓 Otwórz nowy rachunek</h3>
    <form method="post" class="przelew-form">
        <label for="rodzaj">Wybierz typ rachunku:</label>
        <select name="rodzaj_rachunku" id="rodzaj" required>
            <option value="">-- wybierz --</option>
            <option value="Oszczednosciowy">Oszczędnościowy</option>
            <option value="Rozliczeniowy">Rozliczeniowy</option>
            <option value="Walutowy">Walutowy</option>
            <option value="Lokata">Lokata</option>
        </select>
        <button type="submit" name="otworz_rachunek">➕ Otwórz rachunek</button>
    </form>

    <?php if (!empty($komunikat_rachunek)): ?>
        <p class="info"><?= htmlspecialchars($komunikat_rachunek) ?></p>
    <?php endif; ?>
</div>
</body>
</html>
