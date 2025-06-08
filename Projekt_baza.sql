-- ==================== GŁÓWNE OBIEKTY ====================

CREATE TABLE Oddzial (
  ID_oddzialu SERIAL PRIMARY KEY,
  Nazwa VARCHAR(100) NOT NULL,
  Adres TEXT NOT NULL,
  Miasto VARCHAR(100) NOT NULL
);

CREATE TABLE Klient (
  ID_klienta SERIAL PRIMARY KEY,
  PESEL VARCHAR(11) UNIQUE NOT NULL,
  Imie VARCHAR(50) NOT NULL,
  Nazwisko VARCHAR(50) NOT NULL,
  Adres TEXT NOT NULL,
  Email VARCHAR(100) NOT NULL,
  Telefon VARCHAR(20) NOT NULL
);

CREATE TABLE Rachunek (
  Nr_rachunku SERIAL PRIMARY KEY,
  Rodzaj_rachunku VARCHAR(50) NOT NULL 
  CHECK (Rodzaj_rachunku IN ('Oszczednosciowy', 'Rozliczeniowy', 'Walutowy', 'Lokata')),
  Saldo DECIMAL(15,2) DEFAULT 0.00
);

CREATE TABLE Dane_logowania (
  Login VARCHAR(50) PRIMARY KEY UNIQUE NOT NULL,
  Haslo VARCHAR(255) NOT NULL
);

CREATE TABLE Pracownik (
  ID_pracownika SERIAL PRIMARY KEY,
  Imie VARCHAR(50) NOT NULL,
  Nazwisko VARCHAR(50) NOT NULL,
  Stanowisko VARCHAR(50) NOT NULL,
  Data_zatrudnienia DATE DEFAULT CURRENT_DATE
);

CREATE TABLE Przelew (
  ID_przelewu SERIAL PRIMARY KEY,
  Nr_rachunku_nadawcy INT NOT NULL REFERENCES Rachunek(Nr_rachunku),
  Nr_rachunku_odbiorcy INT NOT NULL REFERENCES Rachunek(Nr_rachunku),
  Data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  Kwota DECIMAL(15,2) NOT NULL,
  Tytul TEXT
);

CREATE TABLE Log_systemowy (
  ID_loga SERIAL PRIMARY KEY,
  Czas TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  Typ_zdarzenia VARCHAR(100) NOT NULL,
  Szczegoly TEXT
);

CREATE TABLE Kredyt (
  ID_kredytu SERIAL PRIMARY KEY,
  Kwota DECIMAL(15,2) NOT NULL,
  Liczba_rat INT NOT NULL,
  Oprocentowanie DECIMAL(5,2) NOT NULL,
  Data_udzielenia DATE DEFAULT CURRENT_DATE
);

CREATE TABLE Rata_kredytowa (
  ID_raty SERIAL PRIMARY KEY,
  Numer_raty INT NOT NULL,
  Kwota_raty DECIMAL(10,2) NOT NULL,
  Data_splaty DATE NOT NULL,
  Status_splaty VARCHAR(50) DEFAULT 'Niezaplacona' NOT NULL 
  CHECK (Status_splaty IN ('Zaplacona', 'Niezaplacona', 'Po terminie'))
);

-- ==================== RELACJE ====================

CREATE TABLE Klient_Oddzial (
  Id_klienta INT REFERENCES Klient(ID_klienta),
  ID_oddzialu INT REFERENCES Oddzial(ID_oddzialu),
  PRIMARY KEY (Id_klienta, ID_oddzialu)
);

CREATE TABLE Pracownik_Oddzial (
  ID_pracownika INT REFERENCES Pracownik(ID_pracownika),
  ID_oddzialu INT REFERENCES Oddzial(ID_oddzialu),
  PRIMARY KEY (ID_pracownika, ID_oddzialu)
);

CREATE TABLE Klient_Logowanie (
  Klient INT UNIQUE REFERENCES Klient(ID_klienta),
  Login VARCHAR(50) UNIQUE REFERENCES Dane_logowania(Login)
);

CREATE TABLE Pracownik_Logowanie (
  ID_pracownika INT UNIQUE REFERENCES Pracownik(ID_pracownika),
  Login VARCHAR(50) UNIQUE REFERENCES Dane_logowania(Login),
  PRIMARY KEY (ID_pracownika, Login)
);

CREATE TABLE Kredyt_Klient (
  Id_kredyt INT REFERENCES Kredyt(ID_kredytu),
  Id_klienta INT REFERENCES Klient(ID_klienta),
  PRIMARY KEY (Id_kredyt, Id_klienta)
);

CREATE TABLE Rata_Kredytowa_Kredyt (
  ID_raty INT REFERENCES Rata_kredytowa(ID_raty),
  ID_kredytu INT REFERENCES Kredyt(ID_kredytu),
  PRIMARY KEY (ID_raty, ID_kredytu)
);

CREATE TABLE Log_Klient (
  ID_loga INT REFERENCES Log_systemowy(ID_loga),
  ID_klienta INT REFERENCES Klient(ID_klienta),
  PRIMARY KEY (ID_loga, ID_klienta)
);

CREATE TABLE Log_Pracownik (
  ID_loga INT REFERENCES Log_systemowy(ID_loga),
  ID_pracownika INT REFERENCES Pracownik(ID_pracownika),
  PRIMARY KEY (ID_loga, ID_pracownika)
);

CREATE TABLE Klient_Rachunek (
  Id_klient INT REFERENCES Klient(ID_klienta),
  Nr_rachunku INT REFERENCES Rachunek(Nr_rachunku),
  PRIMARY KEY (Id_klient, Nr_rachunku)
);