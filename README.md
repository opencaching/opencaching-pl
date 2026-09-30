Visit our Wiki:
https://github.com/opencaching/opencaching-pl/wiki

## Uruchamianie lokalne (Docker)

Wymagany jest Docker Desktop z obsługą Docker Compose. Przed pierwszym
uruchomieniem skopiuj `.env.example` do `.env` i ustaw własne hasła w
`DB_PASSWORD` oraz `DB_ROOT_PASSWORD`.

Z katalogu projektu uruchom:

```sh
docker compose up -d --build
```

Aplikacja będzie dostępna pod adresem <http://localhost:8080>, a phpMyAdmin
pod <http://localhost:8081>. W phpMyAdminie użyj `DB_USER` i `DB_PASSWORD`
z pliku `.env`.

Przy kolejnych uruchomieniach (bez przebudowy obrazu):

```sh
docker compose up -d
```

Aby sprawdzić status kontenerów lub logi:

```sh
docker compose ps
docker compose logs -f
```

Aby zatrzymać kontenery:

```sh
docker compose down
```

`docker compose down` zachowuje dane bazy i pliki aplikacji w wolumenach.
**Nie używaj `docker compose down -v`, jeśli chcesz zachować te dane.**
