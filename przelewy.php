<?php
require 'przelew_logika.php';
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

    <?php if ($wiadomosc): ?>
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
