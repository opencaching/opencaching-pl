# AGENTS.md

Instructions for AI agents working in this repository.

## What this project is

A legacy PHP application — OpenCaching PL. It runs on several regional copies
(instances). Each copy has **a different PHP and database version**, so code must
work on the oldest supported environment, not only on the one you happen to test on.

### Regional copies

| Copy | Region            | PHP                        | Database                                                           |
|------|-------------------|----------------------------|--------------------------------------------------------------------|
| OCPL | Poland            | 8.3.36                     | 11.8.9-MariaDB-ubu2604 (mariadb.org binary distribution)           |
| OCNL | Netherlands       | php-8.2                    | MariaDB 10.0                                                       |
| OCUK | United Kingdom    | 8.3.36                     | 11.8.9 (MariaDB)                                                   |
| OCRO | Romania           | patched 8.2.35             | MariaDB 10.0                                                       |
| OCUS | United States     | patched 8.2.35             | MariaDB 10.0                                                       |

Status: OCRO and OCUS run on patched PHP 8.2.35, OCNL runs on php-8.2.

### Compatibility

- Minimum PHP version: **8.1**
- Minimum database version: **MariaDB 10.0** (OCNL, OCRO, OCUS)

This means you must not use functions/constructs available only in newer PHP,
nor SQL functions introduced after MariaDB 10.0 (e.g. CTEs, window functions,
newer JSON functions). If you are unsure whether something is available in 10.0 —
assume it is not, or verify it explicitly.

## Branches and git

- You work on whatever branch dev gives you (the branch you are currently on). Do not
  change it without dev's consent.
- **Do not switch branches** and **do not create new branches** during work.
- **Do not make commits without the user's consent.**
- **Do not delete or overwrite uncommitted changes** (e.g. `git checkout --`,
  `git reset --hard`, `git clean`) — someone's WIP may be in the working tree.
- Do not `push`, rewrite history, or rebase without explicit consent.

## Views and translations

- Views use translations: every user-visible text goes through **`tr()`**.
- Values rendered in HTML are escaped with **`htmlspecialchars()`**.
- Translations needed in JavaScript are passed to the frontend through
  **`data-ln-*`** attributes (e.g. `data-ln-some-key="..."`), not through inline
  scripts containing text.
- **Translations are added only in `lib/languages/en.php`.**
  Do not add keys in other language files or at the call site.

## CSS / SCSS

- Style sources are in **`public/css/*.scss`** (SCSS). The compiled entry point is
  `public/css/style.scss` -> `public/css/style.css`; `style.scss` imports
  `bootstrap.scss` (`flex.scss`, `matrix.scss`), `custom-buttons.scss`,
  `margin-and-paddings.scss`, `admin-power-trail.scss`, `mixins.scss` and
  `responsive.scss` (all in `public/css/`).
- Edit `.scss` source files, **never** the generated `*.css` / `*.css.map`
  files — a later compilation overwrites them. If a CSS file has manual changes,
  move them into the sources first.
- After adding or changing anything in SCSS, **regenerate the CSS**. The
  compiler (`ruby-sass`) runs inside the `app` container, so run from the project
  directory with the `app` container running:
  - One-off build (defaults to `public/css/style.scss` -> `public/css/style.css`):
    `docker compose exec app sass-build`
  - Watcher (rebuilds on every change; stop with Ctrl+C), in a separate terminal:
    `docker compose exec app sass-watch`
  - Custom source/output pair with watcher:
    `docker compose exec app sass-watch SOURCE OUTPUT`
- Scripts `sass-build`/`sass-watch` live in `docker/` and are installed into the
  image as `/usr/local/bin` commands; rebuild the image after changing them.
- SCSS runs with `--no-cache`, so no cache directory is created.

## TypeScript / JavaScript

- TypeScript sources are in **`resources/ts/`** (entry point `resources/ts/index.ts`).
- They are bundled (esbuild) into a single file **`public/js/public.js`**.
  Type checking is done with `tsc` (`npm run typecheck`).
- `public/js/public.js` must be rebuilt after cloning and after every change in
  `resources/ts/` — pages that load it (e.g. `Admin.PowerTrailAdmin/editPowerTrail`)
  return HTTP 500 without it.
- Node/TypeScript are **not** installed in the PHP/Apache image; they run in a
  separate `ts-watch` container (image `node:22-alpine`, profile `assets` in
  `compose.yaml`) that mounts the project and runs `npm ci` itself.
- Regenerate from the project directory:
  - One-off build (type check + bundle):
    `docker compose --profile assets run --rm ts-watch sh -c "npm ci && npm run build:ts"`
  - Watcher (rebuilds `public/js/public.js` on every change; stop with Ctrl+C):
    `docker compose --profile assets run --rm ts-watch`
  - Local (Node.js 22+), without Docker:
    `npm install` then `npm run build:ts` (one-off) or `npm run watch:ts` (watcher)
  - Type check only: `npm run typecheck`
- The watcher does not run `tsc`, so it will not report type errors — run
  `npm run build:ts` or `npm run typecheck` before committing.

## Indentation

- Use **tabs** for indentation in code, with a tab width of **4 spaces**.

## Nature of the code

- The code is **legacy**. Do not rewrite whole modules on the side, do not change
  conventions "because it's more modern" — fit into the surroundings and change
  only what the task requires.
- Keep existing naming patterns, directory layout and file inclusion style.
- If you must touch an old file, keep the diff as small and focused as possible.

## Structure for new code

New code should not be dumped into old directories "as is". We try to follow this
structure:

```
src/
  Controllers/   # controllers
  Services/      # business logic / services
  Models/        # repositories and data access models
  Views/         # views
```

- Controllers: `src/Controllers/...`
- Services: `src/Services/...`
- Repositories: `src/Models/...`
- Views: `src/Views/...`

Legacy code stays where it is — new things are added under `src/` when the task
allows it. `src/` also contains helper directories `Enums`, `Libs`, `Utils`.

## Working rules

- Read the surrounding code before changing it and mimic its style.
- Do not add dependencies (composer/npm) without consent.
- Do not change regional environment configuration.
- If a task requires a decision that breaks the above rules — ask, don't guess.
