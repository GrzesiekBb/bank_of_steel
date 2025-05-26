<?php
require 'db.php';
$blad = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $email = $_POST['email'];
    $pesel = $_POST['pesel'];
    $telefon = $_POST['telefon'];
    $adres = $_POST['adres'];
    $login = $_POST['login'];
    $haslo = password_hash($_POST['haslo'], PASSWORD_DEFAULT);

    $typ_konta_id = $_POST['typ_konta_id'];
    $oddzial_id = $_POST['oddzial_id'];

    $stmt = $pdo->prepare("SELECT rejestruj_klienta(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $imie,
        $nazwisko,
        $email,
        $pesel,
        $telefon,
        $adres,
        $login,
        $haslo,
        $typ_konta_id,
        $oddzial_id
    ]);

    $wynik = $stmt->fetchColumn();

    if (str_starts_with($wynik, 'OK')) {
        header("Location: login.php?nowe=1");
        exit;
    } else {
        $blad = $wynik;
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
        <h2>📝 Rejestracja nowego konta</h2>

        <?php if ($blad): ?>
            <p class="error"><?= htmlspecialchars($blad) ?></p>
        <?php endif; ?>

        <form method="post" class="login-form">
            <table class="form-table">
                <tr>
                    <td><label for="imie">Imię:</label></td>
                    <td><input type="text" name="imie" id="imie" required></td>
                </tr>
                <tr>
                    <td><label for="nazwisko">Nazwisko:</label></td>
                    <td><input type="text" name="nazwisko" id="nazwisko" required></td>
                </tr>
                <tr>
                    <td><label for="email">Email:</label></td>
                    <td><input type="email" name="email" id="email" required></td>
                </tr>
                <tr>
                    <td><label for="pesel">PESEL:</label></td>
                    <td><input type="text" name="pesel" id="pesel" required></td>
                </tr>
                <tr>
                    <td><label for="telefon">Telefon:</label></td>
                    <td><input type="text" name="telefon" id="telefon" required></td>
                </tr>
                <tr>
                    <td><label for="adres">Adres:</label></td>
                    <td><input type="text" name="adres" id="adres" required></td>
                </tr>
                <tr>
                    <td><label for="oddzial">Oddział:</label></td>
                    <td>
                        <select name="oddzial_id" id="oddzial" required>
                            <option value="">-- Wybierz oddział --</option>
                            <?php
                            $stmt = $pdo->query("SELECT id_oddzialu, nazwa, miasto FROM oddzial");
                            foreach ($stmt as $row) {
                                echo "<option value=\"{$row['id_oddzialu']}\">{$row['nazwa']} ({$row['miasto']})</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="typ_konta">Typ konta:</label></td>
                    <td>
                        <select name="typ_konta_id" id="typ_konta" required>
                            <option value="">-- Wybierz typ konta --</option>
                            <?php
                            $stmt = $pdo->query("SELECT id_typu_konta, nazwa FROM typ_konta");
                            foreach ($stmt as $row) {
                                echo "<option value=\"{$row['id_typu_konta']}\">{$row['nazwa']}</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="login">Login:</label></td>
                    <td><input type="text" name="login" id="login" required></td>
                </tr>
                <tr>
                    <td><label for="haslo">Hasło:</label></td>
                    <td><input type="password" name="haslo" id="haslo" required></td>
                </tr>
            </table>

            <button type="submit">Zarejestruj się</button>
        </form>

        <p style="margin-top:1rem;">Masz już konto? <a href="login.php">Zaloguj się</a></p>
    </div>

</body>
</html>
