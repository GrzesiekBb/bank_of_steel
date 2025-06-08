-- ==================== Oddziały ====================
INSERT INTO Oddzial (Nazwa, Adres, Miasto) VALUES
('Oddział Warszawa', 'ul. Marszałkowska 10', 'Warszawa'),
('Oddział Kraków', 'ul. Floriańska 5', 'Kraków');

-- ==================== Klienci ====================
INSERT INTO Klient (PESEL, Imie, Nazwisko, Adres, Email, Telefon) VALUES
('90010112345', 'Jan', 'Kowalski', 'ul. Słoneczna 5, Warszawa', 'jan.kowalski@email.com', '501123456'),
('85050554321', 'Anna', 'Nowak', 'ul. Kwiatowa 10, Kraków', 'anna.nowak@email.com', '502654321');

-- ==================== Pracownicy ====================
INSERT INTO Pracownik (Imie, Nazwisko, Stanowisko, Data_zatrudnienia) VALUES
('Tomasz', 'Wiśniewski', 'Doradca klienta', '2022-01-10'),
('Magdalena', 'Zielińska', 'Konsultant', '2023-06-15');

-- ==================== Dane logowania ====================
INSERT INTO Dane_logowania (Login, Haslo) VALUES
('jkowalski', 'haslo123'),
('anowak', 'tajnehaslo'),
('tomasz.w', 'admin123'),
('magda.z', 'pracownik321');

-- ==================== Powiązania logowania ====================
INSERT INTO Klient_Logowanie (Klient, Login) VALUES
(1, 'jkowalski'),
(2, 'anowak');

INSERT INTO Pracownik_Logowanie (ID_pracownika, Login) VALUES
(1, 'tomasz.w'),
(2, 'magda.z');

-- ==================== Rachunki ====================
INSERT INTO Rachunek (Rodzaj_rachunku, Saldo) VALUES
('Oszczednosciowy', 1500.00),
('Rozliczeniowy', 3000.00);

-- ==================== Powiązania klient-rachunek ====================
INSERT INTO Klient_Rachunek (Id_klient, Nr_rachunku) VALUES
(1, 1),
(2, 2);

-- ==================== Przelewy ====================
INSERT INTO Przelew (Nr_rachunku_nadawcy, Nr_rachunku_odbiorcy, Kwota, Tytul) VALUES
(1, 2, 250.00, 'Opłata za czynsz'),
(2, 1, 100.00, 'Zwrot pieniędzy');

-- ==================== Kredyty ====================
INSERT INTO Kredyt (Kwota, Liczba_rat, Oprocentowanie, Data_udzielenia) VALUES
(10000.00, 12, 5.5, '2024-01-15'),
(5000.00, 6, 4.0, '2024-03-01');

-- ==================== Powiązania kredyt-klient ====================
INSERT INTO Kredyt_Klient (Id_kredyt, Id_klienta) VALUES
(1, 1),
(2, 2);

-- ==================== Raty kredytowe ====================
INSERT INTO Rata_kredytowa (Numer_raty, Kwota_raty, Data_splaty, Status_splaty) VALUES
(1, 833.33, '2024-02-15', 'Zaplacona'),
(2, 833.33, '2024-03-15', 'Niezaplacona');

-- ==================== Powiązania rata-kredyt ====================
INSERT INTO Rata_Kredytowa_Kredyt (ID_raty, ID_kredytu) VALUES
(1, 1),
(2, 1);

-- ==================== Przypisania oddziałów ====================
INSERT INTO Klient_Oddzial (Id_klienta, ID_oddzialu) VALUES
(1, 1),
(2, 2);

INSERT INTO Pracownik_Oddzial (ID_pracownika, ID_oddzialu) VALUES
(1, 1),
(2, 2);

-- ==================== Logi systemowe ====================
INSERT INTO Log_systemowy (Typ_zdarzenia, Szczegoly) VALUES
('Logowanie', 'Użytkownik Jan Kowalski zalogował się.'),
('Operacja', 'Zrealizowano przelew na kwotę 250.00 PLN.');

-- ==================== Powiązania logów ====================
INSERT INTO Log_Klient (ID_loga, ID_klienta) VALUES
(1, 1);

INSERT INTO Log_Pracownik (ID_loga, ID_pracownika) VALUES
(2, 1);
