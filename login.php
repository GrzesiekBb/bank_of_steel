<?php
session_start();
require_once 'db.php';

// Ustawienie błędów na czas debugowania (można potem usunąć)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sprawdza hasło z bazy
function sprawdz_haslo($login, $haslo, $pdo) {
    $stmt = $pdo->prepare("SELECT haslo FROM dane_logowania WHERE login = :login");
    $stmt->execute(['login' => $login]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || !password_verify($haslo, $row['haslo'])) {
        return false;
    }
    return true;
}

// Pobiera ID klienta przez funkcję PostgreSQL
function pobierz_id_klienta($login, $pdo) {
    $stmt = $pdo->prepare("SELECT get_id_klienta(:login) AS id_klienta");
    $stmt->execute(['login' => $login]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['id_klienta'] ?? null;
}

$blad = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $haslo = $_POST['haslo'] ?? '';

    if (empty($login) || empty($haslo)) {
        $blad = "Wprowadź login i hasło.";
    } elseif (!sprawdz_haslo($login, $haslo, $pdo)) {
        $blad = "Nieprawidłowy login lub hasło.";
    } else {
        $id_klienta = pobierz_id_klienta($login, $pdo);
        if ($id_klienta) {
            $_SESSION['id_klienta'] = $id_klienta;
            $_SESSION['login'] = $login;
            // Pobierz dane klienta z bazy i zapisz w sesji
            $stmt = $pdo->prepare("SELECT * FROM get_dane_klienta(:id)");
            $stmt->execute(['id' => $id_klienta]);
            $dane = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dane) {
                foreach ($dane as $klucz => $wartosc) {
                    $_SESSION[$klucz] = $wartosc;
                }
            }
            // Logowanie do log_systemowy + log_klient (opcjonalne)
            $stmt = $pdo->prepare("INSERT INTO log_systemowy (Typ_zdarzenia, Szczegoly) VALUES ('Logowanie', :szczegoly)");
            $stmt->execute([
                'szczegoly' => "Użytkownik $login (ID $id_klienta) zalogował się."
            ]);
            $log_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare("INSERT INTO log_klient (ID_loga, ID_klienta) VALUES (:log_id, :klient_id)");
            $stmt->execute(['log_id' => $log_id, 'klient_id' => $id_klienta]);

            header("Location: index.php");
            exit;
        } else {
            $blad = "Błąd systemu — brak ID klienta.";
        }
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
    <div class="login-logo">
        <img src="logo1.png" alt="Logo banku">
    </div>
    <div class="login-container">
        <h2>Logowanie</h2>
        <?php if (!empty($blad)): ?>
            <div class="error"><?= htmlspecialchars($blad) ?></div>
        <?php endif; ?>
        <form method="post" class="login-form">
            <label for="login">Login:</label>
            <input type="text" name="login" id="login" required>

            <label for="haslo">Hasło:</label>
            <input type="password" name="haslo" id="haslo" required>

            <button type="submit">Zaloguj się</button>
        </form>
        <p>Nie masz konta? <a href="rejestracja.php">Zarejestruj się!</a></p>
    </div>
</body>
</html>
