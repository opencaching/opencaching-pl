# Uruchomienie lokalne w Dockerze

Stack zawiera aplikację w Apache/PHP 7.4 oraz osobny kontener MariaDB 11.8.9.
Pusta baza o nazwie ustawionej w `DB_NAME` jest tworzona przy pierwszym starcie;
tabele i dane trzeba zaimportować osobno.

1. Skopiuj `.env.example` do `.env`.
2. Ustaw własne, niepuste hasła w `DB_PASSWORD` i `DB_ROOT_PASSWORD`.
3. Uruchom `docker compose up --build`.
4. Otwórz aplikację pod <http://localhost:8080> (lub portem ustawionym przez `APP_PORT`).
5. Otwórz phpMyAdmin pod <http://localhost:8081> (lub portem z `PMA_PORT`);
   zaloguj się nazwą i hasłem z `DB_USER` i `DB_PASSWORD`.

Pliki bazy i generowane pliki aplikacji są przechowywane w wolumenach Dockera.
`docker compose down` zatrzymuje kontenery, ale zachowuje te dane. Nie używaj
`docker compose down -v`, jeśli chcesz je zachować.

Kontener aplikacji używa `docker/settings.inc.php`, który pobiera połączenie
z bazą ze zmiennych w `.env`. Lokalny, ignorowany przez Git plik
`lib/settings.inc.php` nie jest nadpisywany.
