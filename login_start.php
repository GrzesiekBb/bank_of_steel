<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $haslo = $_POST['haslo'];

    $stmt = $pdo->prepare("SELECT * FROM dane_logowania WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    if ($user && password_verify($haslo, $user['haslo'])) {
        $_SESSION['klient_id'] = $user['id_klienta'];
        $_SESSION['imie'] = $user['imie'];
        header("Location: index.php");
        exit;
    } else {
        $blad = "Nieprawidłowy login lub hasło.";
    }
}
