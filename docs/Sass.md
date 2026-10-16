# Sass

Projekt używa Sass do generowania części arkuszy stylów. Kompilator `ruby-sass`
jest zainstalowany w obrazie aplikacji (kontener `app`, `Dockerfile`), więc nie
trzeba nic instalować lokalnie.

## Pliki źródłowe i wyniki

| Źródło | Wynik | Uwagi |
|---|---|---|
| `public/css/style.scss` | `public/css/style.css` | importuje `bootstrap.scss` i `custom-buttons.scss` (oba w `public/css/`) |
| `public/css/style_responsive.sass` | `public/css/style_responsive.css` | składnia wcięciowa; importuje `../sass/mixins` i `../sass/responsive` |

Pliki `*.css` (i `*.css.map`) są wynikiem kompilacji. Nie edytuj ich ręcznie,
bo kolejna kompilacja je nadpisze. Zmiany rób w źródłach Sass. Jeśli w pliku CSS
są ręczne zmiany, przenieś je najpierw do źródeł.

Uwaga! Plik `public/css/style_responsive.sass` docelowo będzie włączony do pliku `public/css/style.scss`

## Kompilacja w Dockerze

Polecenia uruchamia się z katalogu projektu, przy działającym kontenerze `app`.

Jednorazowa kompilacja (domyślnie `public/css/style.scss` → `public/css/style.css`):

```sh
docker compose exec app sass-build
```

Dowolna para plików, np. dla responsywnego CSS:

```sh
docker compose exec app sass-build public/css/style_responsive.sass public/css/style_responsive.css
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

Sass uruchamiany jest z opcją `--no-cache`, więc nie powstaje katalog
`.sass-cache`.