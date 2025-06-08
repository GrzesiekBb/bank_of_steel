<?php
class Klient {
    public int $id;
    public string $imie;
    public string $nazwisko;
    public string $email;
    public string $pesel;
    public string $telefon;
    public string $adres;
    public string $login;
    public string $haslo;

    public static function zaloguj(string $login, string $haslo, PDO $pdo): ?Klient {
        $stmt = $pdo->prepare("SELECT * FROM klient WHERE login = ? join ");
        $stmt->execute([$login]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($haslo, $row['haslo'])) {
            $klient = new Klient();
            $klient->id = (int)$row['id_klienta'];
            $klient->imie = $row['imie'];
            $klient->nazwisko = $row['nazwisko'];
            $klient->email = $row['email'];
            $klient->pesel = $row['pesel'];
            $klient->telefon = $row['telefon'];
            $klient->adres = $row['adres'];
            return $klient;
        }

        return null;
    }

    public function zmienHaslo(string $noweHaslo, PDO $pdo): void {
        $hashed = password_hash($noweHaslo, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE klient SET haslo = ? WHERE id_klienta = ?");
        $stmt->execute([$hashed, $this->id]);
    }
    public function zmienHasloBezpiecznie(string $noweHaslo, string $potwierdzHaslo, PDO $pdo): string {
        if (strlen($noweHaslo) < 6) {
            return "Hasło musi mieć co najmniej 6 znaków.";
        }
        if ($noweHaslo !== $potwierdzHaslo) {
            return "Hasła się nie zgadzają.";
        }

        $hashed = password_hash($noweHaslo, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE klient SET haslo = ? WHERE id_klienta = ?");
        $stmt->execute([$hashed, $this->id]);

        return "Hasło zostało pomyślnie zmienione.";
    }
}
?>
