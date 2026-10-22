# TypeScript

Kod źródłowy TS jest w `resources/ts/` (punkt wejścia: `resources/ts/index.ts`).
Jest kompilowany (esbuild) do jednego pliku `public/js/public.js`. Typy są
sprawdzane przez `tsc` (`npm run typecheck`). Plik `public/js/public.js` trzeba
zbudować po sklonowaniu repozytorium i po każdej zmianie w `resources/ts/` – bez
niego strony, które go ładują (np. `Admin.PowerTrailAdmin/editPowerTrail`),
zwracają błąd 500.

## Instalacja TypeScripta w Dockerze

TypeScript i esbuild nie są instalowane w obrazie aplikacji (PHP/Apache). Działają
w osobnym kontenerze `ts-watch` (obraz `node:22-alpine`, profil `assets` w
`compose.yaml`), który montuje katalog projektu i sam instaluje zależności
poleceniem `npm ci` (pakiety z `package.json` trafiają do `node_modules/`).
Nic więcej nie trzeba instalować ręcznie.

Jednorazowy build, bez watchera:

```sh
docker compose --profile assets run --rm ts-watch sh -c "npm ci && npm run build:ts"
```

Build z watcherem (przebudowuje `public/js/public.js` po każdej zmianie w
`resources/ts/`; zakończ `Ctrl+C`):

```sh
docker compose --profile assets run --rm ts-watch
```

## Uruchomienie lokalne (bez Dockera)

Wymagany jest Node.js 22 (lub nowszy) z npm. W katalogu projektu:

```sh
npm install      # lub: npm ci
```

Jednorazowy build (najpierw sprawdzenie typów `tsc`, potem esbuild):

```sh
npm run build:ts
```

Build z watcherem (zakończ `Ctrl+C`):

```sh
npm run watch:ts
```

Samo sprawdzenie typów, bez budowania pliku:

```sh
npm run typecheck
```

Watcher (`watch:ts`) nie uruchamia `tsc`, więc nie zgłasza błędów typów, tylko
błędy składni. Przed commitem uruchom `npm run build:ts` albo `npm run typecheck`.
