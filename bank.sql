-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Maj 23, 2025 at 09:36 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bank`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klient`
--

CREATE TABLE `klient` (
  `id_klienta` int(11) NOT NULL,
  `imie` varchar(50) DEFAULT NULL,
  `nazwisko` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `pesel` varchar(11) DEFAULT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `adres` varchar(255) DEFAULT NULL,
  `login` varchar(50) DEFAULT NULL,
  `haslo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `klient`
--

INSERT INTO `klient` (`id_klienta`, `imie`, `nazwisko`, `email`, `pesel`, `telefon`, `adres`, `login`, `haslo`) VALUES
(1, 'Jan', 'Kowalski', 'jan.kowalski@example.com', '90010112345', '123456789', 'ul. Przykładowa 1', 'jank', 'haslo123'),
(2, 'Anna', 'Nowak', 'anna.nowak@example.com', '92020223456', '987654321', 'ul. Testowa 2', 'annan', 'haslo456'),
(4, 'Grzegorz', 'Barna', 'grzegorzbarna80@gmail.com', '03726584927', '784749506', 'os. Bolesława śmiałego 3/138 60-682 Poznań', 'barnag', '$2y$10$/wXUvTJZJQQKYSp6ckdh/OKj.5fMJQbKLDIrJZKkO9IHc3XsK4eoC');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klient_oddzial`
--

CREATE TABLE `klient_oddzial` (
  `id_klienta` int(11) NOT NULL,
  `id_oddzialu` int(11) NOT NULL,
  `data_przypisania` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `klient_oddzial`
--

INSERT INTO `klient_oddzial` (`id_klienta`, `id_oddzialu`, `data_przypisania`) VALUES
(1, 1, '2025-05-23'),
(2, 2, '2025-05-23');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `konto`
--

CREATE TABLE `konto` (
  `id_konta` int(11) NOT NULL,
  `numer_konta` varchar(26) DEFAULT NULL,
  `id_klienta` int(11) DEFAULT NULL,
  `id_typu_konta` int(11) DEFAULT NULL,
  `saldo` decimal(10,2) DEFAULT NULL,
  `data_utworzenia` date DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `konto`
--

INSERT INTO `konto` (`id_konta`, `numer_konta`, `id_klienta`, `id_typu_konta`, `saldo`, `data_utworzenia`, `status`) VALUES
(1, '12345678901234567890123456', 1, 1, 5000.00, '2025-05-23', 'aktywne'),
(2, '65432109876543210987654321', 2, 2, 2400.50, '2025-05-23', 'aktywne'),
(3, '81206434305099061486119729', 4, 2, 0.00, '2025-05-23', 'aktywne');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `logi`
--

CREATE TABLE `logi` (
  `id_logu` int(11) NOT NULL,
  `id_klienta` int(11) DEFAULT NULL,
  `akcja` varchar(100) DEFAULT NULL,
  `data_akcji` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logi`
--

INSERT INTO `logi` (`id_logu`, `id_klienta`, `akcja`, `data_akcji`) VALUES
(1, 1, 'Zalogowano do systemu', '2025-05-23 20:21:19'),
(2, 1, 'Wysłano przelew', '2025-05-23 20:21:19'),
(3, 2, 'Zmieniono hasło', '2025-05-23 20:21:19');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `oddzial`
--

CREATE TABLE `oddzial` (
  `id_oddzialu` int(11) NOT NULL,
  `nazwa` varchar(100) DEFAULT NULL,
  `miasto` varchar(50) DEFAULT NULL,
  `adres` varchar(255) DEFAULT NULL,
  `telefon` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `oddzial`
--

INSERT INTO `oddzial` (`id_oddzialu`, `nazwa`, `miasto`, `adres`, `telefon`) VALUES
(1, 'Oddział Centralny', 'Warszawa', 'ul. Główna 1', '123-456-789'),
(2, 'Oddział Zachodni', 'Poznań', 'ul. Zachodnia 5', '987-654-321');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `przelew`
--

CREATE TABLE `przelew` (
  `id_przelewu` int(11) NOT NULL,
  `id_nadawcy` int(11) DEFAULT NULL,
  `id_odbiorcy` int(11) DEFAULT NULL,
  `kwota` decimal(10,2) DEFAULT NULL,
  `tytul` varchar(100) DEFAULT NULL,
  `data_przelewu` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `przelew`
--

INSERT INTO `przelew` (`id_przelewu`, `id_nadawcy`, `id_odbiorcy`, `kwota`, `tytul`, `data_przelewu`, `status`) VALUES
(1, 1, 2, 300.00, 'Zwrot za pizzę', '2025-05-23 20:21:18', 'Zrealizowany');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `transakcja`
--

CREATE TABLE `transakcja` (
  `id_transakcji` int(11) NOT NULL,
  `id_konta` int(11) DEFAULT NULL,
  `typ` varchar(30) DEFAULT NULL,
  `kwota` decimal(10,2) DEFAULT NULL,
  `data_transakcji` datetime DEFAULT NULL,
  `opis` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transakcja`
--

INSERT INTO `transakcja` (`id_transakcji`, `id_konta`, `typ`, `kwota`, `data_transakcji`, `opis`) VALUES
(1, 1, 'Wpłata', 1000.00, '2025-05-23 20:21:18', 'Wpłata gotówki'),
(2, 1, 'Wypłata', 200.00, '2025-05-23 20:21:18', 'Bankomat'),
(3, 2, 'Wpłata', 1500.50, '2025-05-23 20:21:18', 'Przelew wynagrodzenia'),
(4, 2, 'Wypłata', 100.00, '2025-05-23 20:21:18', 'Zakupy spożywcze');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `typ_konta`
--

CREATE TABLE `typ_konta` (
  `id_typu_konta` int(11) NOT NULL,
  `nazwa` varchar(50) DEFAULT NULL,
  `oprocentowanie` decimal(5,2) DEFAULT NULL,
  `opis` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `typ_konta`
--

INSERT INTO `typ_konta` (`id_typu_konta`, `nazwa`, `oprocentowanie`, `opis`) VALUES
(1, 'Oszczędnościowe', 1.50, 'Konto oszczędnościowe z oprocentowaniem'),
(2, 'Rachunek bieżący', 0.00, 'Standardowe konto do codziennego użytku'),
(3, 'admin', 0.00, 'Konto administratora systemu'),
(4, 'uzytkownik', 0.00, 'Zwykły klient banku'),
(5, 'pracownik', 0.00, 'Pracownik banku z uprawnieniami dostępu');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `klient`
--
ALTER TABLE `klient`
  ADD PRIMARY KEY (`id_klienta`),
  ADD UNIQUE KEY `pesel` (`pesel`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indeksy dla tabeli `klient_oddzial`
--
ALTER TABLE `klient_oddzial`
  ADD PRIMARY KEY (`id_klienta`,`id_oddzialu`),
  ADD KEY `id_oddzialu` (`id_oddzialu`);

--
-- Indeksy dla tabeli `konto`
--
ALTER TABLE `konto`
  ADD PRIMARY KEY (`id_konta`),
  ADD UNIQUE KEY `numer_konta` (`numer_konta`),
  ADD KEY `id_klienta` (`id_klienta`),
  ADD KEY `id_typu_konta` (`id_typu_konta`);

--
-- Indeksy dla tabeli `logi`
--
ALTER TABLE `logi`
  ADD PRIMARY KEY (`id_logu`),
  ADD KEY `id_klienta` (`id_klienta`);

--
-- Indeksy dla tabeli `oddzial`
--
ALTER TABLE `oddzial`
  ADD PRIMARY KEY (`id_oddzialu`);

--
-- Indeksy dla tabeli `przelew`
--
ALTER TABLE `przelew`
  ADD PRIMARY KEY (`id_przelewu`),
  ADD KEY `id_nadawcy` (`id_nadawcy`),
  ADD KEY `id_odbiorcy` (`id_odbiorcy`);

--
-- Indeksy dla tabeli `transakcja`
--
ALTER TABLE `transakcja`
  ADD PRIMARY KEY (`id_transakcji`),
  ADD KEY `id_konta` (`id_konta`);

--
-- Indeksy dla tabeli `typ_konta`
--
ALTER TABLE `typ_konta`
  ADD PRIMARY KEY (`id_typu_konta`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `klient`
--
ALTER TABLE `klient`
  MODIFY `id_klienta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `konto`
--
ALTER TABLE `konto`
  MODIFY `id_konta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `logi`
--
ALTER TABLE `logi`
  MODIFY `id_logu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `oddzial`
--
ALTER TABLE `oddzial`
  MODIFY `id_oddzialu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `przelew`
--
ALTER TABLE `przelew`
  MODIFY `id_przelewu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transakcja`
--
ALTER TABLE `transakcja`
  MODIFY `id_transakcji` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `typ_konta`
--
ALTER TABLE `typ_konta`
  MODIFY `id_typu_konta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `klient_oddzial`
--
ALTER TABLE `klient_oddzial`
  ADD CONSTRAINT `klient_oddzial_ibfk_1` FOREIGN KEY (`id_klienta`) REFERENCES `klient` (`id_klienta`),
  ADD CONSTRAINT `klient_oddzial_ibfk_2` FOREIGN KEY (`id_oddzialu`) REFERENCES `oddzial` (`id_oddzialu`);

--
-- Constraints for table `konto`
--
ALTER TABLE `konto`
  ADD CONSTRAINT `konto_ibfk_1` FOREIGN KEY (`id_klienta`) REFERENCES `klient` (`id_klienta`),
  ADD CONSTRAINT `konto_ibfk_2` FOREIGN KEY (`id_typu_konta`) REFERENCES `typ_konta` (`id_typu_konta`);

--
-- Constraints for table `logi`
--
ALTER TABLE `logi`
  ADD CONSTRAINT `logi_ibfk_1` FOREIGN KEY (`id_klienta`) REFERENCES `klient` (`id_klienta`);

--
-- Constraints for table `przelew`
--
ALTER TABLE `przelew`
  ADD CONSTRAINT `przelew_ibfk_1` FOREIGN KEY (`id_nadawcy`) REFERENCES `konto` (`id_konta`),
  ADD CONSTRAINT `przelew_ibfk_2` FOREIGN KEY (`id_odbiorcy`) REFERENCES `konto` (`id_konta`);

--
-- Constraints for table `transakcja`
--
ALTER TABLE `transakcja`
  ADD CONSTRAINT `transakcja_ibfk_1` FOREIGN KEY (`id_konta`) REFERENCES `konto` (`id_konta`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
