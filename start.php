<?php
session_start();
require 'db.php';

if (!isset($_SESSION['klient_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['klient_id'];
$imie = $_SESSION['klient_imie'];

// Pobieranie kont użytkownika
$stmt = $pdo->prepare("
    SELECT k.*, t.nazwa AS typ_konta 
    FROM konto k
    JOIN typ_konta t ON k.id_typu_konta = t.id_typu_konta
    WHERE k.id_klienta = :id
");
$stmt->execute(['id' => $id]);
$konta = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>