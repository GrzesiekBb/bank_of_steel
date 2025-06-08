<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_klienta'])) {
    header('Location: login.php');
    exit;
}

$id_klienta = $_SESSION['id_klienta'];
$wiadomosc = '';
$przelewy = [];

// Pobierz numery rachunków klienta (potrzebne do wybrania nadawcy)
$stmt = $pdo->prepare("
    SELECT r.nr_rachunku
    FROM rachunek r
    JOIN klient_rachunek kr ON kr.nr_rachunku = r.nr_rachunku
    WHERE kr.id_klient = :id
");
$stmt->execute(['id' => $id_klienta]);
$rachunki = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Wysyłanie przelewu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numer_odbiorcy'], $_POST['kwota'], $_POST['tytul'])) {
    $nr_odbiorcy = $_POST['numer_odbiorcy'];
    $kwota = floatval($_POST['kwota']);
    $tytul = trim($_POST['tytul']);

    // Zakładamy, że pierwszy rachunek klienta to rachunek domyślny
    $nr_nadawcy = $rachunki[0] ?? null;

    if (!$nr_nadawcy) {
        $wiadomosc = "Brak przypisanego rachunku.";
    } elseif ($nr_nadawcy == $nr_odbiorcy) {
        $wiadomosc = "Nie możesz wykonać przelewu na ten sam rachunek.";
    } elseif ($kwota <= 0) {
        $wiadomosc = "Kwota przelewu musi być większa niż 0.";
    } else {
        $stmt = $pdo->prepare("SELECT wykonaj_przelew_fun(:nadawca, :odbiorca, :kwota, :tytul) AS wynik");
        $stmt->execute([
            'nadawca' => $nr_nadawcy,
            'odbiorca' => $nr_odbiorcy,
            'kwota' => $kwota,
            'tytul' => $tytul
        ]);
        $rezultat = $stmt->fetchColumn();
        $wiadomosc = $rezultat;
    }
}

// Pobranie historii przelewów klienta
$stmt = $pdo->prepare("SELECT * FROM get_przelewy(:id)");
$stmt->execute(['id' => $id_klienta]);
$wyniki = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Formatowanie danych przelewów
foreach ($wyniki as $przelew) {
    $przelewy[] = [
        'data_przelewu' => $przelew['data_przelewu'],
        'odbiorca_nr' => $przelew['nr_konta_odbiorcy'],
        'nadawca_nr' => $przelew['nr_konta_nadawcy'],
        'kwota' => $przelew['kwota'],
        'tytul' => $przelew['tytul'],
        'status' => ($przelew['nr_konta_nadawcy'] == $rachunki[0]) ? 'Wysłano' : 'Odebrano'
    ];
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>💸 Historia przelewów</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>📜 Twoje przelewy</h2>

<?php if (!empty($wiadomosc)): ?>
    <p class="info"><?= htmlspecialchars($wiadomosc) ?></p>
<?php endif; ?>

<?php if (empty($przelewy)): ?>
    <p>Brak przelewów do wyświetlenia.</p>
<?php else: ?>
    <table class="tabela-przelewow">
        <thead>
        <tr>
            <th>Data</th>
            <th>Odbiorca</th>
            <th>Nadawca</th>
            <th>Kwota</th>
            <th>Tytuł</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($przelewy as $przelew): ?>
            <tr>
                <td><?= date('Y-m-d H:i', strtotime($przelew['data_przelewu'])) ?></td>
                <td><?= htmlspecialchars($przelew['odbiorca_nr']) ?></td>
                <td><?= htmlspecialchars($przelew['nadawca_nr']) ?></td>
                <td><?= number_format($przelew['kwota'], 2) ?> PLN</td>
                <td><?= htmlspecialchars($przelew['tytul']) ?></td>
                <td><?= htmlspecialchars($przelew['status']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<div class="page-container">
    <h3>💸 Wykonaj nowy przelew</h3>
    <form method="post" class="przelew-form">
        <label for="numer_odbiorcy">Numer konta odbiorcy:</label><br>
        <input type="text" name="numer_odbiorcy" id="numer_odbiorcy" required><br>

        <label for="kwota">Kwota (PLN):</label><br>
        <input type="number" step="0.01" name="kwota" id="kwota" required><br>

        <label for="tytul">Tytuł przelewu:</label><br>
        <input type="text" name="tytul" id="tytul" required><br>

        <button type="submit">Wyślij przelew</button>
    </form>
    <a class="button" href="index.php">⬅️ Powrót</a>
</div>
</body>
</html>
