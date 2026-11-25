<?php

use src\Utils\Uri\SimpleRouter;
use src\Enums\GpxExportFilterEnum;

/** @var \src\Utils\View\View $view */

?>

<div class="content2-pagetitle">
	<img src="/images/blue/merit_badge.png" class="icon32 align-middle" alt="" title="" />&nbsp;
	<a href="<?= htmlspecialchars(SimpleRouter::getLink('Badges.Badges'), ENT_QUOTES, 'UTF-8'); ?>"><?= tr('merit_badges'); ?></a>
	/
	<?= htmlspecialchars($view->badge['name'] ?? 'show', ENT_QUOTES, 'UTF-8'); ?>
</div>

<div class="badge-v2-top">
	<div class="badge-v2-ring">
		<svg class="ring_v2 ring_v2--yellow ring_v2--thick" viewBox="0 0 140 140">
			<circle class="ring_v2__progress" style="--percent: 100" cx="70" cy="70" r="67.5" />
		</svg>
		<?php
		$badgeImages = $view->badgeImages ?? [];
		$view->badgeImages = $badgeImages;
		if (count($badgeImages) <= 1) {
			echo $view->component('badges/badge_single_img', ['badgeImages' => $badgeImages]);
		} else {
			echo $view->component('badges/badge_gallery_img', ['badgeImages' => $badgeImages]);
		}
		?>
	</div>
	<div class="badge-v2-title">
		<?= htmlspecialchars($view->badge['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
	</div>
</div>

<?php if (!empty($view->badgeDescription)) { ?>
<div class="badge-v2-frame mb-20">
	<?php if (!empty($view->badge['long_name'])) { ?>
		<h3 class="mb-10 text-center"><?= htmlspecialchars($view->badge['long_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
	<?php } ?>
	<div><?= $view->badgeDescription; ?></div>
</div>
<?php } ?>

<?php if (!empty($view->recommendedCaches) || !empty($view->titledCaches)) { ?>
<div class="badge-v2-frame mb-20">
	<?php if (!empty($view->activeCount)) { ?>
	<div class="badge-v2-frame-full">
		<p class="m-0"><?= tr('badge_active_caches_count'); ?> <?= (int) $view->activeCount; ?></p>
	</div>
	<?php } ?>
	<?php if (!empty($view->recommendedCaches)) { ?>
	<div class="badge-v2-frame-light">
		<?= tr('badge_recommended_caches'); ?>
		<table class="badge-v2-table">
			<thead>
				<tr>
					<th><?= tr('No.'); ?></th>
					<th><?= tr('name_label'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach (array_values($view->recommendedCaches) as $i => $cache) { ?>
				<tr>
					<td><?= $i + 1; ?></td>
					<td>
						<a href="/viewcache.php?cacheid=<?= (int) ($cache['cache_id'] ?? 0); ?>">
							<?= htmlspecialchars($cache['cache_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
						</a>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php } ?>
	<?php if (!empty($view->titledCaches)) { ?>
	<div class="badge-v2-frame-light">
		<?= tr('badge_cache_of_week'); ?>
		<table class="badge-v2-table">
			<thead>
				<tr>
					<th><?= tr('No.'); ?></th>
					<th><?= tr('name_label'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach (array_values($view->titledCaches) as $i => $cache) { ?>
				<tr>
					<td><?= $i + 1; ?></td>
					<td>
						<a href="/viewcache.php?cacheid=<?= (int) ($cache['cache_id'] ?? 0); ?>">
							<?= htmlspecialchars($cache['cache_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
						</a>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php } ?>
</div>
<?php } ?>

<?php if (true) { ?>
<div class="badge-v2-frame mb-20">
	<div class="badge-v2-levels-gainers">
		<div class="badge-v2-levels">
			<table class="badge-v2-table badge-v2-table--counter w-100 badge-v2-table--flat">
				<thead>
					<tr>
						<th></th>
						<th><?= tr('merit_badge_level'); ?></th>
						<th><?= tr('merit_badge_level_name'); ?></th>
						<th><?= tr('merit_badge_number_threshold'); ?></th>
						<th><?= tr('merit_badge_gain_count'); ?></th>
						<th><?= tr('merit_badge_gain_last_date'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$levels = $view->levels ?? [];
					$levelsCount = count($levels);
					foreach ($levels as $level) {
						$lvl = (int) ($level['level'] ?? 0);
						$idx = $levelsCount > 0 ? (int) round(($lvl + 1) * 10 / $levelsCount) : 1;
						if ($idx > 10) { $idx = 10; }
						if ($idx <= 0) { $idx = 1; }
						$colorClass = 'badge-level-' . $idx;
						$gain = $level['gain_counter'] ?? 0;
						$lastDate = !empty($level['gain_last_date']) ? date('d-m-Y', strtotime((string) $level['gain_last_date'])) : '';
					?>
					<tr class="badge-level-row" data-level="<?= $lvl; ?>">
						<td></td>
						<td class="<?= $colorClass; ?>"></td>
						<td class="<?= $colorClass; ?>"><?= htmlspecialchars($level['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?= (int) ($level['threshold'] ?? 0); ?></td>
						<td><?= (int) $gain; ?></td>
						<td><?= htmlspecialchars($lastDate, ENT_QUOTES, 'UTF-8'); ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div id="badge-gainers-frame" class="badge-v2-gainers hidden">
				<table class="badge-v2-table w-100 badge-v2-table--flat" id="gainers-table">
					<thead>
						<tr>
							<th><?= tr('user'); ?></th>
							<th><?= tr('merit_badge_number'); ?></th>
							<th><?= tr('merit_badge_gain_date'); ?></th>
						</tr>
					</thead>
					<tbody id="gainers-tbody"></tbody>
				</table>
				<div id="gainers-pagination" class="mt-5"></div>
			</div>
		</div>
	</div>
<?php } ?>

<div id="gainers-cache-store" class="hidden"></div>
<div id="badge-gainers-root" class="hidden" data-badge-id="<?= (int) ($view->badge['id'] ?? 0); ?>"></div>

<div class="badge-v2-frame mb-20 text-center">
	<div>
		<?php if (!empty($view->badge['show_positions_list'])) { ?>
		<a class="blue-button-small" href="<?= SimpleRouter::getLink('Badges.BadgePositions', null, (int) ($view->badge['id'] ?? 0)); ?>/"><?= tr('merit_badge_list'); ?></a>
		<?php } ?>
		<?php if (!empty($view->userId)) { ?>
		<button type="button" data-toggle-gpx class="blue-button-small"><?= tr('merit_badge_export_gpx'); ?></button>
		<?php if (!empty($view->badge['is_obtainable'])) { ?>
		<button type="button" class="blue-button-small badge-recalculate-btn" data-badge-id="<?= (int) ($view->badge['id'] ?? 0); ?>" data-user-id="<?= (int) $view->userId; ?>"><?= tr('merit_badge_check_my_level'); ?></button>
		<?php } ?>
		<?php } ?>
		<?php if (!empty($view->badge['show_positions_map'])) { ?>
		<a class="green-button-small" href="<?= SimpleRouter::getLink('Badges.BadgePositionsMap', null, (int) ($view->badge['id'] ?? 0)); ?>/"><?= tr('merit_badge_map'); ?></a>
		<?php } ?>
	</div>
	<form id="badgeGpxOptions" action="/search.php" method="get" class="mb-15 mt-10 text-left collapsed">
		<input type="hidden" name="searchto" value="searchbybadge">
		<input type="hidden" name="showresult" value="1">

		<input type="hidden" name="count" value="500">
		<input type="hidden" name="idBadge" value="<?= (int) ($view->badge['id'] ?? 0); ?>">
		<div class="mb-10">
			<label><input id="f_<?= GpxExportFilterEnum::INACTIVE->value; ?>" name="f_inactive" type="checkbox" value="1" checked /> <?= tr('badge_gpx_inactive'); ?></label>
		</div>
		<div class="mb-10">
			<label><input id="f_<?= GpxExportFilterEnum::IGNORED->value; ?>" name="f_ignored" type="checkbox" value="1" checked /> <?= tr('badge_gpx_ignored'); ?></label>
		</div>
		<div class="mb-10">
			<label><input id="f_<?= GpxExportFilterEnum::OWNED->value; ?>" name="f_userowner" type="checkbox" value="1" checked /> <?= tr('badge_gpx_owner'); ?></label>
		</div>
		<div class="mb-10">
			<label><input id="f_<?= GpxExportFilterEnum::WATCHED->value; ?>" name="f_watched" type="checkbox" value="1" checked /> <?= tr('badge_gpx_watched'); ?></label>
		</div>
		<div class="mb-10">
			<label><input id="f_<?= GpxExportFilterEnum::FOUND->value; ?>" name="f_userfound" type="checkbox" value="1" checked /> <?= tr('badge_gpx_found'); ?></label>
		</div>
		<div class="mb-10">
			<?= tr('badge_gpx_log_limit'); ?>: <input id="gpxLogLimit" type="range" min="1" max="50" value="5" oninput="document.getElementById('gpxLogLimitCurrent').innerText=this.value" /> <span id="gpxLogLimitCurrent">5</span>
		</div>
		<div>
			<button type="submit" name="output" value="gpx" class="blue-button-small"><?= tr('badge_gpx_button'); ?></button>
			<button type="submit" name="output" value="zip" class="blue-button-small"><?= tr('badge_gpx_garmin'); ?></button>
		</div>
	</form>
</div>

<?php if (!empty($view->badgeOwners)) { ?>
<div class="badge-v2-frame mb-20 text-center">
	<?php foreach ($view->badgeOwners as $ownerGroup) { ?>
		<div class="px-5 py-2 fs-14" style="display: inline-block;">
			<strong><?= tr((string) $ownerGroup['translationKey']); ?>:</strong>
			<?php foreach ($ownerGroup['owners'] as $owner) { ?>
				<span class="comma-after"><a href="/viewprofile.php?userid=<?= (int) $owner['id']; ?>" target="_blank"><?= htmlspecialchars((string) $owner['username'], ENT_QUOTES, 'UTF-8'); ?></a></span>
			<?php } ?>
		</div>
	<?php } ?>
</div>
<?php } ?>
