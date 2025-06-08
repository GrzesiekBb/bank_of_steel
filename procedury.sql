CREATE OR REPLACE PROCEDURE zmien_haslo(
    z_id_klienta INT,
    z_haslo VARCHAR
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_login VARCHAR;
BEGIN
    SELECT dl.Login INTO v_login
	From dane_logowania as dl
	join Klient_logowanie kl on kl.login = dl.login
	join Klient k on kl.klient=k.id_klienta
	where k.id_klienta = z_id_klienta limit 1;

    IF v_login IS NULL THEN
        RAISE EXCEPTION 'Brak powiązanego loginu dla klienta o ID %', z_id_klienta;
    END IF;

    UPDATE Dane_logowania
    SET Haslo = z_haslo
    WHERE Login = v_login;
END;
$$;
----------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION dodaj_log_zmiany_hasla()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    INSERT INTO Log_systemowy (Typ_zdarzenia, Szczegoly)
    VALUES (
        'Zmiana hasła',
        FORMAT('Zmieniono hasło dla loginu: %s', NEW.Login)
    );
    RETURN NEW;
END;
$$;

CREATE TRIGGER log_zmiany_hasla
AFTER UPDATE OF Haslo ON Dane_logowania
FOR EACH ROW
WHEN (OLD.Haslo IS DISTINCT FROM NEW.Haslo)
EXECUTE FUNCTION dodaj_log_zmiany_hasla();

---------------------------------------------------
CREATE OR REPLACE FUNCTION get_dane_klienta(v_id_klienta INT)
RETURNS TABLE (
    imie varchar,
    nazwisko varchar,
    adres TEXT,
    email varchar,
    telefon varchar,
    nazwa varchar,
    adres_oddzial TEXT,
    miasto varchar
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM klient WHERE id_klienta = v_id_klienta
    ) THEN
        RETURN; 
    END IF;

    RETURN QUERY
    SELECT 
        k.imie,
        k.nazwisko,
        k.adres,
        k.email,
        k.telefon,
        o.nazwa,
        o.adres,
        o.miasto
    FROM klient AS k
    JOIN klient_oddzial ko ON ko.id_klienta = k.id_klienta
    JOIN oddzial o ON o.id_oddzialu = ko.id_oddzialu
    WHERE k.id_klienta = v_id_klienta;
END;
$$;
--------------------------------------------------------------
CREATE OR REPLACE FUNCTION get_id_klienta(v_login VARCHAR)
RETURNS INT
LANGUAGE plpgsql
AS $$
DECLARE
    wynik INT;
BEGIN
    SELECT k.id_klienta INTO wynik
    FROM dane_logowania AS dl
    JOIN klient_logowanie kl ON kl.login = dl.login
    JOIN klient k ON kl.klient = k.id_klienta
    WHERE dl.login = v_login
    LIMIT 1;

    RETURN wynik;

EXCEPTION
    WHEN OTHERS THEN
        RETURN NULL;
END;
$$;
-------------------------------------------------------------------

CREATE OR REPLACE FUNCTION get_przelewy(v_id_klienta INT)
RETURNS TABLE (
    nr_konta_nadawcy INT,
    nr_konta_odbiorcy INT,
    data_przelewu TIMESTAMP,
    kwota NUMERIC,
    tytul TEXT
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM klient WHERE id_klienta = v_id_klienta
    ) THEN
        RETURN;
    END IF;

    RETURN QUERY
    SELECT 
        p.nr_rachunku_nadawcy,
        p.nr_rachunku_odbiorcy,
        p.data,
        p.kwota,
        p.tytul
    FROM klient AS k
    JOIN klient_rachunek kr ON k.id_klienta = kr.id_klient
    JOIN rachunek r ON r.nr_rachunku = kr.nr_rachunku
    JOIN przelew p ON 
        p.nr_rachunku_nadawcy = r.nr_rachunku 
        OR p.nr_rachunku_odbiorcy = r.nr_rachunku
    WHERE k.id_klienta = v_id_klienta;
END;
$$;

-------------------------------------------------------------------

CREATE OR REPLACE FUNCTION get_oddzial()
RETURNS TABLE (
	Nazwa varchar,
	Adres text,
	Miasto varchar
)
LANGUAGE plpgsql
AS $$
BEGIN
	return QUERY 
	Select o.Nazwa,o.Adres,o.Miasto from oddzial as o;

END;
$$;
-----------------------------------------
CREATE OR REPLACE FUNCTION get_logi_klienta(v_id_klienta INT)
RETURNS TABLE (
    czas TIMESTAMP,
    typ_zdarzenia VARCHAR,
    szczegoly TEXT
)
AS $$
BEGIN
    RETURN QUERY
    SELECT l.czas, l.typ_zdarzenia, l.szczegoly
    FROM log_systemowy l
    JOIN log_klient lk ON lk.id_loga = l.id_loga
    WHERE lk.id_klienta = v_id_klienta
    ORDER BY l.czas DESC;
END;
$$ LANGUAGE plpgsql;