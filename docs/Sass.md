# SCSS

Projekt używa SCSS do generowania arkuszy stylów. Kompilator `ruby-sass`
jest zainstalowany w obrazie aplikacji (kontener `app`, `Dockerfile`), więc nie
trzeba nic instalować lokalnie.

## Pliki źródłowe i wyniki

| Źródło | Wynik | Uwagi |
|---|---|---|
| `public/css/style.scss` | `public/css/style.css` | importuje `bootstrap.scss` (`flex.scss`, `matrix.scss`), `custom-buttons.scss`, `margin-and-paddings.scss`, `admin-power-trail.scss`, `mixins.scss` i `responsive.scss` (wszystkie w `public/css/`) |

Pliki `*.css` (i `*.css.map`) są wynikiem kompilacji. Nie edytuj ich ręcznie,
bo kolejna kompilacja je nadpisze. Zmiany rób w źródłach SCSS. Jeśli w pliku CSS
są ręczne zmiany, przenieś je najpierw do źródeł.

## Kompilacja w Dockerze

Polecenia uruchamia się z katalogu projektu, przy działającym kontenerze `app`.

Jednorazowa kompilacja (domyślnie `public/css/style.scss` → `public/css/style.css`):

```sh
docker compose exec app sass-build
```

Watcher, który kompiluje po każdej zmianie w źródle (zakończ `Ctrl+C`).
Uruchom go w osobnym terminalu:

```sh
docker compose exec app sass-watch
```

Domyślnie obserwuje `public/css/style.scss`. Jeśli pliku jeszcze nie ma, watcher
poczeka na jego utworzenie. Można też wskazać parę plików:

```sh
docker compose exec app sass-watch SOURCE OUTPUT
```

Skrypty `sass-build` i `sass-watch` są w katalogu `docker/` i trafiają do obrazu
jako polecenia w `/usr/local/bin`. Po zmianie skryptów trzeba przebudować obraz
(`docker compose up --build`).

SCSS uruchamiany jest z opcją `--no-cache`, więc nie powstaje katalog
cache.