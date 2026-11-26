<?php
use src\Utils\Uri\SimpleRouter;
$badge = $view->badge ?? null;
$levels = $view->levels ?? [];
?>
<div class="content2-pagetitle">
    <img src="/images/blue/merit_badge.png" class="icon32 align-middle" alt="" />&nbsp;
    <?= tr('badge_edit'); ?>
</div>
<div class="content2-container">
    <?php if (!empty($view->message)) { ?>
        <div class="callout callout-success mb-15"><?= htmlspecialchars($view->message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php } ?>
    <?php if (!empty($view->error)) { ?>
        <div class="callout callout-danger mb-15"><?= htmlspecialchars($view->error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php } ?>

    <form id="badgeEditForm" method="post" action="<?= SimpleRouter::getLink('Admin.BadgesAdmin', 'save', (int) ($badge['id'] ?? 0)); ?>"></form>

    <div class="mb-10 d-flex align-items-center">
        <label for="badge_name" class="w-25 flex-shrink-0 mr-10"><?= tr('name_label'); ?></label>
        <input id="badge_name" form="badgeEditForm" type="text" name="name" maxlength="30" value="<?= htmlspecialchars($badge['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control flex-grow-1" />
    </div>

    <div class="mb-10 d-flex align-items-center">
        <label for="badge_category" class="w-25 flex-shrink-0 mr-10">Kategoria</label>
        <select id="badge_category" form="badgeEditForm" name="category_id" class="form-control flex-grow-1">
            <?php foreach (($view->categories ?? []) as $cat) { ?>
            <option value="<?= (int) $cat['id']; ?>" <?= ((int)($badge['category_id'] ?? 0) === (int)$cat['id']) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($cat['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-10 d-flex align-items-center">
        <label for="badge_sequence" class="w-25 flex-shrink-0 mr-10"><?= tr('sequence'); ?></label>
        <input id="badge_sequence" form="badgeEditForm" type="number" name="sequence" value="<?= (int) ($badge['sequence'] ?? 0); ?>" class="form-control flex-grow-1" />
    </div>

    <h3 class="mt-20">Obrazek domyślny</h3>
    <?php if (!empty($badge['picture'])) { ?>
        <img data-badge-image src="<?= htmlspecialchars($badge['picture'][0] === '/' ? $badge['picture'] : '/' . $badge['picture'], ENT_QUOTES, 'UTF-8'); ?>" alt="" />
    <?php } else { ?>
        <img data-badge-image src="" alt="" class="hidden" />
    <?php } ?>
    <?php if (isset($view->badgeImageUploadModelJson)) { ?>
    <div id="badge-image-upload-root"
         data-badge-image-upload="1"
         data-badge-id="<?= (int) ($badge['id'] ?? 0); ?>"
         data-upload-model="<?= htmlspecialchars((string) $view->badgeImageUploadModelJson, ENT_QUOTES, 'UTF-8'); ?>"
         data-ln-preview="<?= htmlspecialchars(tr('preview'), ENT_QUOTES, 'UTF-8'); ?>"
         data-ln-delete="<?= htmlspecialchars(tr('delete'), ENT_QUOTES, 'UTF-8'); ?>"
         data-ln-none="brak"></div>
    <button type="button" class="blue-button-small badge-image-upload-btn mt-10" data-badge-id="<?= (int) ($badge['id'] ?? 0); ?>">Dodaj obrazek</button>
    <?php } ?>

    <h3 class="mt-20">Levele</h3>
    <table class="table table-striped full-width">
        <thead>
            <tr>
                <th>Level</th>
                <th>Nazwa</th>
                <th>Threshold</th>
                <th>Obrazek</th>
                <th>Opcje</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($levels as $lvl) { ?>
            <tr>
                <td><?= (int) ($lvl['level'] ?? 0); ?></td>
                <td>
                    <input type="text" form="badgeEditForm" name="level_name[<?= (int) ($lvl['level'] ?? 0); ?>]" maxlength="30" value="<?= htmlspecialchars($lvl['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control" />
                </td>
                <td><?= (int) ($lvl['threshold'] ?? 0); ?></td>
                <td data-level-image="<?= (int) ($lvl['level'] ?? 0); ?>">
                    <?php if (!empty($lvl['picture'])) { ?>
                        <a href="<?= htmlspecialchars($lvl['picture'][0] === '/' ? $lvl['picture'] : '/' . $lvl['picture'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><?= tr('preview'); ?></a>
                    <?php } else { ?>
                        brak
                    <?php } ?>
                </td>
                <td>
                    <button type="button" class="blue-button-small badge-level-upload-btn" data-badge-id="<?= (int) ($badge['id'] ?? 0); ?>" data-level="<?= (int) ($lvl['level'] ?? 0); ?>">Dodaj obrazek</button>
                    <?php if (!empty($lvl['picture'])) { ?>
                        <form method="post" action="<?= SimpleRouter::getLink('Admin.BadgesAdmin', 'deleteLevelImage', [(int) ($badge['id'] ?? 0), (int) ($lvl['level'] ?? 0)]); ?>" data-confirm="Confirm delete image from badge level?">
                            <button type="submit" class="blue-button-small" data-confirm="Confirm delete image from badge level?">Wykasuj obrazek</button>
                        </form>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="mt-10 d-flex align-items-center flex-wrap">
        <button type="submit" form="badgeEditForm" class="blue-button-small mr-10"><?= tr('save'); ?></button>
        <a class="orange-button-small mr-10" href="<?= SimpleRouter::getLink('Admin.BadgesAdmin'); ?>"><?= tr('back'); ?></a>
    </div>

    <?php if (isset($view->badgeLevelImageUploadModelJson)) { ?>
    <div id="badge-level-upload-root"
         data-badge-level-upload="1"
         data-badge-id="<?= (int) ($badge['id'] ?? 0); ?>"
         data-upload-model="<?= htmlspecialchars((string) $view->badgeLevelImageUploadModelJson, ENT_QUOTES, 'UTF-8'); ?>"
         data-ln-preview="<?= htmlspecialchars(tr('preview'), ENT_QUOTES, 'UTF-8'); ?>"
         data-ln-delete="<?= htmlspecialchars(tr('delete'), ENT_QUOTES, 'UTF-8'); ?>"
         data-ln-none="brak"></div>
    <?php } ?>
</div>
