# CSS progress ring for merit badges (new architecture)

Legacy code still uses the jQuery `asPieProgress` SVG ring. The new copy should use
this pure-CSS ring instead. No jQuery, no JS, no `asPieProgress`.

## Markup

```html
<div class="Badge-pie-progress badge-level-3"
     role="progressbar"
     style="--ring-size: 140px; --progress: 42%"
     aria-valuemin="0" aria-valuemax="100" aria-valuenow="42">
  <div class="pie_progress__content">
    <img src="..." alt="" />
  </div>
</div>
```

- `badge-level-1` … `badge-level-10` sets the ring colour via `--ring-color`
  (defined in `public/css/badge.scss`).
- `--ring-size` controls the diameter (default 140px; use 60px for the small variant
  `Badge-pie-progress-small`).
- `--progress` is the filled percentage (0–100).

## Animation

The ring grows in over 3 seconds (`badge-ring-grow` keyframes, ease-out).
Progress is shown as a `conic-gradient` from `--ring-color` to `#d9d9d9`.

## Calculating the values (PHP, new copy)

```php
$max = ($nextVal === MeritBadge::THE_HIGHEST_LEVEL) ? 1 : ($nextVal - $prevThreshold);
$curr = ($nextVal === MeritBadge::THE_HIGHEST_LEVEL) ? 1 : ($currVal - $prevThreshold);
$progress = $max > 0 ? (int) min(100, round($curr / $max * 100)) : 100;

$idx = $levelsNumber > 0 ? (int) round(($level + 1) * 10 / $levelsNumber) : 1;
$idx = max(1, min(10, $idx));
$levelClass = 'badge-level-' . $idx;
```

Render:
```php
'<div class="Badge-pie-progress ' . $levelClass . '" '
. 'style="--ring-size: ' . $size . 'px; --progress: ' . $progress . '%" ...>'
```

## Files

- Styles: `public/css/badge.scss` (imported by `bootstrap.scss`, compiled into
  `public/css/style.css`).
- No JavaScript is required. Do not add `asPieProgress` scripts in new views.
