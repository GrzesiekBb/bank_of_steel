<?php
require 'start.php';

if (!isset($_SESSION['id_klienta'])) {
    header('Location: login.php');
    exit;
}

$id_klienta = $_SESSION['id_klienta'];

// Pobierz wszystkie rachunki klienta
$stmt = $pdo->prepare("
    SELECT r.nr_rachunku, r.rodzaj_rachunku, r.saldo
    FROM rachunek r
    JOIN klient_rachunek kr ON kr.nr_rachunku = r.nr_rachunku
    WHERE kr.id_klient = :id
");
$stmt->execute(['id' => $id_klienta]);
$konta = $stmt->fetchAll(PDO::FETCH_ASSOC);
$komunikat_kredyt = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wez_kredyt'])) {
    $kwota = floatval($_POST['kwota'] ?? 0);
    $raty = intval($_POST['raty'] ?? 0);
    $id_klienta = $_SESSION['id_klienta'];

    if ($kwota <= 0 || $raty <= 0) {
        $komunikat_kredyt = "Kwota i liczba rat muszą być większe niż 0.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT wes_kredyt_fun(:kwota, :raty, :klient_id) AS wynik");
            $stmt->execute([
                'kwota' => $kwota,
                'raty' => $raty,
                'klient_id' => $id_klienta
            ]);
            $komunikat_kredyt = $stmt->fetchColumn();
        } catch (PDOException $e) {
            $komunikat_kredyt = "Błąd: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Panel klienta</title>
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
        <h2>👋 Witaj, <?= htmlspecialchars($_SESSION['imie']) ?>!</h2>
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
                <option value="raty.php">💳 Raty</option>
            </select>
        </div>
    </div>
</div>

<h3>Twoje konta</h3>
<?php foreach ($konta as $konto): ?>
    <div class="konto-box">
        <p><strong>Rodzaj rachunku:</strong> <?= htmlspecialchars($konto['rodzaj_rachunku']) ?></p>
        <p><strong>Numer rachunku:</strong> <?= htmlspecialchars($konto['nr_rachunku']) ?></p>
        <p><strong>Saldo:</strong> <?= number_format($konto['saldo'], 2) ?> PLN</p>
    </div>
<?php endforeach; ?>
<div class="page-container">
    <h3>💰 Weź kredyt</h3>
    <form method="post" class="przelew-form">
        <label for="kwota">Kwota kredytu (PLN):</label>
        <input type="number" step="0.01" name="kwota" id="kwota" required>

        <label for="raty">Liczba rat:</label>
        <input type="number" name="raty" id="raty" required>

        <button type="submit" name="wez_kredyt">Zatwierdź</button>
    </form>

    <?php if (!empty($komunikat_kredyt)): ?>
        <p class="info"><?= htmlspecialchars($komunikat_kredyt) ?></p>
    <?php endif; ?>
</div>

</body>
</html>
