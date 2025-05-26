<?php
require 'start.php';

$wiadomosc = '';
$id_klienta = $_SESSION['klient_id'];

// pobieramy ID konta użytkownika
$stmt = $pdo->prepare("SELECT id_konta, numer_konta FROM konto WHERE id_klienta = :id");
$stmt->execute(['id' => $id_klienta]);
$konta = $stmt->fetchAll(PDO::FETCH_ASSOC);

$moje_id_konta = $konta[0]['id_konta'] ?? null;
$moj_nr_konta = $konta[0]['numer_konta'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numer_odbiorcy = $_POST['numer_odbiorcy'] ?? '';
    $kwota = $_POST['kwota'] ?? '';
    $tytul = $_POST['tytul'] ?? '';

    if ($moj_nr_konta) {
        $query = "SELECT wykonaj_przelew(:nadawca, :odbiorca, :kwota, :tytul)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'nadawca' => $moj_nr_konta,
            'odbiorca' => $numer_odbiorcy,
            'kwota' => $kwota,
            'tytul' => $tytul
        ]);
        $wiadomosc = $stmt->fetchColumn();
    } else {
        $wiadomosc = "Błąd: nie znaleziono konta nadawcy.";
    }
}

// POBIERANIE HISTORII PRZELEWÓW
$stmt = $pdo->prepare("
    SELECT p.*, k1.numer_konta AS nadawca_nr, k2.numer_konta AS odbiorca_nr
    FROM przelew p
    JOIN konto k1 ON p.id_nadawcy = k1.id_konta
    JOIN konto k2 ON p.id_odbiorcy = k2.id_konta
    WHERE p.id_nadawcy = :id OR p.id_odbiorcy = :id
    ORDER BY p.data_przelewu DESC
");
$stmt->execute(['id' => $moje_id_konta]);
$przelewy = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>