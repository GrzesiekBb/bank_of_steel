CREATE OR REPLACE FUNCTION wykonaj_przelew_fun(
    wp_nr_rachunku_nadawcy INT,
    wp_nr_rachunku_odbiorcy INT,
    wp_kwota NUMERIC,
    wp_tytul TEXT
)
RETURNS TEXT AS $$
DECLARE
    saldo_nadawcy NUMERIC;
BEGIN
    SELECT saldo INTO saldo_nadawcy FROM rachunek WHERE nr_rachunku = wp_nr_rachunku_nadawcy;
    IF NOT FOUND THEN
        RETURN 'Błąd: Rachunek nadawcy nie istnieje';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM rachunek WHERE nr_rachunku = wp_nr_rachunku_odbiorcy) THEN
        RETURN 'Błąd: Rachunek odbiorcy nie istnieje';
    END IF;

    IF saldo_nadawcy < wp_kwota THEN
        RETURN 'Błąd: Brak wystarczających środków';
    END IF;

    UPDATE rachunek SET saldo = saldo - wp_kwota WHERE nr_rachunku = wp_nr_rachunku_nadawcy;
    UPDATE rachunek SET saldo = saldo + wp_kwota WHERE nr_rachunku = wp_nr_rachunku_odbiorcy;

    INSERT INTO przelew (nr_rachunku_nadawcy, nr_rachunku_odbiorcy, kwota, tytul)
    VALUES (wp_nr_rachunku_nadawcy, wp_nr_rachunku_odbiorcy, wp_kwota, wp_tytul);

    RETURN 'OK: Przelew wykonany';
EXCEPTION
    WHEN OTHERS THEN
        RETURN 'Błąd systemowy: ' || SQLERRM;
END;
$$ LANGUAGE plpgsql;

-- Funkcja logująca przelew
CREATE OR REPLACE FUNCTION loguj_przelew()
RETURNS TRIGGER AS $$
BEGIN
  INSERT INTO Log_systemowy (Typ_zdarzenia, Szczegoly)
  VALUES (
    'Wykonano przelew',
    'Przelew z rachunku ' || NEW.Nr_rachunku_nadawcy ||
    ' do rachunku ' || NEW.Nr_rachunku_odbiorcy ||
    ' na kwotę ' || NEW.Kwota || ' PLN. Tytuł: "' || COALESCE(NEW.Tytul, 'brak') || '"'
  );
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger po dodaniu przelewu
CREATE TRIGGER trg_loguj_przelew
AFTER INSERT ON Przelew
FOR EACH ROW
EXECUTE FUNCTION loguj_przelew();

---------------------------------------------------


CREATE OR REPLACE PROCEDURE otworz_rachunek_proc(
    p_id_klienta INT,
    p_rodzaj_rachunku VARCHAR
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_nr_rachunku INT;
BEGIN
    INSERT INTO Rachunek (Rodzaj_rachunku, Saldo)
    VALUES (p_rodzaj_rachunku, 0.00)
    RETURNING Nr_rachunku INTO v_nr_rachunku;

    INSERT INTO Klient_Rachunek (Id_klient, Nr_rachunku)
    VALUES (p_id_klienta, v_nr_rachunku);
END;
$$;

-------------------------------------------------

CREATE OR REPLACE FUNCTION rejestracja_klienta_fun(
    p_imie VARCHAR,
    p_nazwisko VARCHAR,
    p_pesel VARCHAR,
    p_adres TEXT,
    p_email VARCHAR,
    p_telefon VARCHAR,
    p_id_oddzialu INT,
    p_login VARCHAR,
    p_haslo VARCHAR
) RETURNS TEXT AS $$
DECLARE
    v_id_klienta INT;
BEGIN
    IF length(p_pesel) != 11 OR p_pesel ~ '[^0-9]' THEN
        RETURN 'Błąd: PESEL musi mieć dokładnie 11 cyfr.';
    END IF;

    IF position('@' in p_email) = 0 THEN
        RETURN 'Błąd: Email musi zawierać znak @.';
    END IF;

    IF length(p_login) < 4 THEN
        RETURN 'Błąd: Login musi mieć co najmniej 4 znaki.';
    END IF;

    IF EXISTS (SELECT 1 FROM Klient WHERE PESEL = p_pesel) THEN
        RETURN 'Błąd: PESEL już istnieje.';
    END IF;

    IF EXISTS (SELECT 1 FROM Dane_logowania WHERE Login = p_login) THEN
        RETURN 'Błąd: Login już istnieje.';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM Oddzial WHERE ID_oddzialu = p_id_oddzialu) THEN
        RETURN 'Błąd: Nieprawidłowy oddział.';
    END IF;

    INSERT INTO Klient (PESEL, Imie, Nazwisko, Adres, Email, Telefon)
    VALUES (p_pesel, p_imie, p_nazwisko, p_adres, p_email, p_telefon)
    RETURNING ID_klienta INTO v_id_klienta;

    INSERT INTO Klient_Oddzial (Id_klienta, ID_oddzialu)
    VALUES (v_id_klienta, p_id_oddzialu);

    INSERT INTO Dane_logowania (Login, Haslo)
    VALUES (p_login, p_haslo);

    INSERT INTO Klient_Logowanie (Klient, Login)
    VALUES (v_id_klienta, p_login);

    RETURN 'Rejestracja zakończona sukcesem.';
EXCEPTION
    WHEN OTHERS THEN
        RETURN 'Błąd: ' || SQLERRM;
END;
$$ LANGUAGE plpgsql;

------------------------------------------------------

CREATE OR REPLACE FUNCTION wes_kredyt_fun(
    p_kwota NUMERIC,
    p_liczba_rat INT,
    p_id_klienta INT
) RETURNS TEXT AS $$
DECLARE
    v_id_kredytu INT;
    v_id_raty INT;
    v_kwota_raty NUMERIC;
    v_data_splaty DATE := CURRENT_DATE;
    v_nr_rachunku INT;
    i INT;
BEGIN
    -- Sprawdzenie, czy klient istnieje
    IF NOT EXISTS (SELECT 1 FROM Klient WHERE Id_klienta = p_id_klienta) THEN
        RETURN 'Błąd: Klient o podanym ID nie istnieje.';
    END IF;

    -- Szukamy rachunku rozliczeniowego przypisanego do klienta
    SELECT r.Nr_rachunku INTO v_nr_rachunku
    FROM Rachunek r 
    JOIN Klient_Rachunek kr ON r.Nr_rachunku = kr.Nr_rachunku
    WHERE kr.Id_klient = p_id_klienta AND r.Rodzaj_rachunku = 'Rozliczeniowy' 
    LIMIT 1;

    -- Jeśli brak rachunku rozliczeniowego, tworzymy nowy i przypisujemy klientowi
    IF v_nr_rachunku IS NULL THEN
        CALL otworz_rachunek_proc(p_id_klienta, 'Rozliczeniowy');

        SELECT r.Nr_rachunku INTO v_nr_rachunku 
        FROM Rachunek r 
        JOIN Klient_Rachunek kr ON r.Nr_rachunku = kr.Nr_rachunku
        WHERE kr.Id_klient = p_id_klienta AND r.Rodzaj_rachunku = 'Rozliczeniowy' 
        ORDER BY r.Nr_rachunku DESC 
        LIMIT 1;
    END IF;

    -- Tworzenie kredytu
    INSERT INTO Kredyt (Kwota, Liczba_rat, Oprocentowanie)
    VALUES (p_kwota, p_liczba_rat, 9.0)
    RETURNING ID_kredytu INTO v_id_kredytu;

    INSERT INTO Kredyt_Klient (Id_kredyt, Id_klienta)
    VALUES (v_id_kredytu, p_id_klienta);

    -- Obliczenie i utworzenie rat
    v_kwota_raty := ROUND(p_kwota / p_liczba_rat, 2);

    FOR i IN 1..p_liczba_rat LOOP
        INSERT INTO Rata_kredytowa (Numer_raty, Kwota_raty, Data_splaty)
        VALUES (i, v_kwota_raty, v_data_splaty + (INTERVAL '1 month' * (i - 1)))
        RETURNING ID_raty INTO v_id_raty;

        INSERT INTO Rata_Kredytowa_Kredyt (ID_raty, ID_kredytu)
        VALUES (v_id_raty, v_id_kredytu);
    END LOOP;

    -- Wpłata środków z kredytu na rachunek rozliczeniowy
    UPDATE Rachunek 
    SET Saldo = Saldo + p_kwota 
    WHERE Nr_rachunku = v_nr_rachunku;

    RETURN 'Kredyt i raty zostały utworzone. Środki dodano do rachunku nr ' || v_nr_rachunku || '.';
END;
$$ LANGUAGE plpgsql;
-----------------------------------------------------
CREATE OR REPLACE FUNCTION trg_log_kredyt_po_polaczeniu() RETURNS trigger AS $$
DECLARE
    v_log_id INT;
BEGIN
    INSERT INTO Log_systemowy (Typ_zdarzenia, Szczegoly)
    VALUES (
        'Kredyt',
        'Klient ID ' || NEW.Id_klienta || ' uzyskał kredyt ID ' || NEW.Id_kredyt
    )
    RETURNING ID_loga INTO v_log_id;

    INSERT INTO Log_Klient (ID_loga, ID_klienta)
    VALUES (v_log_id, NEW.Id_klienta);

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_after_insert_kredyt_klient
AFTER INSERT ON Kredyt_Klient
FOR EACH ROW
EXECUTE FUNCTION trg_log_kredyt_po_polaczeniu();
-----------------------------------------------------
CREATE OR REPLACE FUNCTION trg_log_splata_raty_fn() RETURNS trigger AS $$
DECLARE
    v_klient_id INT;
    v_log_id INT;
BEGIN
    IF NEW.Status_splaty = 'Zaplacona' AND OLD.Status_splaty IS DISTINCT FROM 'Zaplacona' THEN
        SELECT kc.Id_klienta
        INTO v_klient_id
        FROM Rata_Kredytowa_Kredyt rkk
        JOIN Kredyt_Klient kc ON rkk.ID_kredytu = kc.Id_kredyt
        WHERE rkk.ID_raty = NEW.ID_raty
        LIMIT 1;

        IF v_klient_id IS NOT NULL THEN
            INSERT INTO Log_systemowy (Typ_zdarzenia, Szczegoly)
            VALUES (
                'Zapłata raty',
                'Klient ID ' || v_klient_id || ' zapłacił ratę ID ' || NEW.ID_raty
            )
            RETURNING ID_loga INTO v_log_id;

            INSERT INTO Log_Klient (ID_loga, ID_klienta)
            VALUES (v_log_id, v_klient_id);
        END IF;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_after_update_rata_splata
AFTER UPDATE ON Rata_kredytowa
FOR EACH ROW
EXECUTE FUNCTION trg_log_splata_raty_fn();
-------------------------------------------------------------------
CREATE OR REPLACE FUNCTION zaplac_rate_z_rachunku(
    p_id_raty INT,
    p_nr_rachunku INT
) RETURNS TEXT AS $$
DECLARE
    v_kwota_raty NUMERIC(15,2);
    v_saldo NUMERIC(15,2);
    v_status VARCHAR(20);
BEGIN
    SELECT Kwota_raty, Status_splaty INTO v_kwota_raty, v_status
    FROM Rata_kredytowa
    WHERE ID_raty = p_id_raty;

    IF NOT FOUND THEN
        RETURN 'Błąd: Nie znaleziono raty o podanym ID.';
    END IF;

    IF v_status = 'Zaplacona' THEN
        RETURN 'Rata jest już zapłacona.';
    END IF;

    SELECT Saldo INTO v_saldo
    FROM Rachunek
    WHERE Nr_rachunku = p_nr_rachunku;

    IF NOT FOUND THEN
        RETURN 'Błąd: Nie znaleziono rachunku o podanym numerze.';
    END IF;

    IF v_saldo < v_kwota_raty THEN
        RETURN 'Błąd: Niewystarczające środki na rachunku.';
    END IF;

    UPDATE Rachunek
    SET Saldo = Saldo - v_kwota_raty
    WHERE Nr_rachunku = p_nr_rachunku;

    UPDATE Rata_kredytowa
    SET Status_splaty = 'Zaplacona'
    WHERE ID_raty = p_id_raty;

    RETURN 'Rata została zapłacona pomyślnie.';
END;
$$ LANGUAGE plpgsql;
------------------------------------------------------------------
