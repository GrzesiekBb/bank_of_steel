<?php
try {
    $pdo = new PDO("pgsql:host=localhost;port=5432;dbname=steel_bank", "postgres", "postgres");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Błąd połączenia z PostgreSQL: " . $e->getMessage());
}
