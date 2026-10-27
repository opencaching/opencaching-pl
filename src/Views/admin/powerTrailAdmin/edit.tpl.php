<?php

use src\Enums\PowerTrailStatusesEnum;
use src\Utils\Uri\SimpleRouter;

$powerTrail = $view->powerTrail;
$type = $view->types[(int) $powerTrail['type']] ?? null;
$statusKey = $view->statuses[(int) $powerTrail['status']] ?? null;
$descriptionForEditor = htmlspecialchars(
    htmlspecialchars_decode((string) $powerTrail['description'], ENT_QUOTES),
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);

$dateCreatedRaw = (string) ($powerTrail['dateCreated'] ?? '');
$dateCreatedDate = substr($dateCreatedRaw, 0, 10);
$dateCreatedInputValue = preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateCreatedDate)
    && $dateCreatedDate !== '0000-00-00'
    ? $dateCreatedDate
    : '';
$dateCreatedDisplay = $dateCreatedDate !== '' ? $dateCreatedDate : '-';

$view->callChunk('tinyMCE', true, '#powerTrailDescriptionEditor');

?>
<div class="content2-container">
    <div class="content2-pagetitle">
        <a href="<?= SimpleRouter::getLink('Admin.PowerTrailAdmin'); ?>">
            <?= tr('menu_octeam_power_trail'); ?>
        </a>
        / <?= tr('menu_octeam_power_trail_edit'); ?>
        / <?= htmlspecialchars($powerTrail['name'], ENT_QUOTES, 'UTF-8'); ?>
    </div>

    <table class="table">
        <tbody>
            <tr>
                <td class="content-title-noshade"><?= tr('pt008'); ?></td>
                <td>
                    <a href="/powerTrail.php?ptAction=showSerie&amp;ptrail=<?= (int) $powerTrail['id']; ?>">
                        <?= htmlspecialchars($powerTrail['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td class="content-title-noshade"><?= tr('pt023'); ?></td>
                <td><?= $type ? tr($type['translationKey']) : (int) $powerTrail['type']; ?></td>
            </tr>
            <tr>
                <td class="content-title-noshade"><?= tr('status_label'); ?></td>
                <td class="powerTrail-status">
                    <span class="powerTrail-status-label"><?= $statusKey ? tr($statusKey) : (int) $powerTrail['status']; ?></span>
                    <button type="button" class="blue-button-small powerTrail-status-change-button"><?= tr('powertrail_admin_change_status'); ?></button>
                    <form class="powerTrail-status-form" method="post" hidden
                          data-ln-error-message="<?= htmlspecialchars(tr('powertrail_admin_change_status_error'), ENT_QUOTES, 'UTF-8'); ?>"
                          action="<?= SimpleRouter::getLink(
                              'Admin.PowerTrailAdmin',
                              'changeStatus',
                              (int) $powerTrail['id']
                          ); ?>">
                        <select name="status">
                            <?php foreach ([
                                PowerTrailStatusesEnum::STATUS_OPEN,
                                PowerTrailStatusesEnum::STATUS_INSERVICE,
                                PowerTrailStatusesEnum::STATUS_CLOSED,
                            ] as $statusId) { ?>
                                <option value="<?= $statusId; ?>"
                                    <?= (int) $powerTrail['status'] === $statusId ? 'selected' : ''; ?>>
                                    <?= tr($view->statuses[$statusId]); ?>
                                </option>
                            <?php } ?>
                        </select>
                        <button type="submit" class="blue-button-small"><?= tr('save'); ?></button>
                    </form>
                </td>
            </tr>
            <tr>
                <td class="content-title-noshade"><?= tr('pt020'); ?></td>
                <td class="powerTrail-cache-count"><?= (int) $powerTrail['cacheCount']; ?></td>
            </tr>
            <tr>
                <td class="content-title-noshade"><?= tr('powertrail_admin_wis'); ?></td>
                <td class="powerTrail-wis">
                    <span class="powerTrail-wis-label"><?= (int) $powerTrail['perccentRequired']; ?></span>
                    <button type="button" class="blue-button-small powerTrail-wis-change-button">
                        <?= tr('powertrail_admin_change_percent_required'); ?>
                    </button>
                    <form class="powerTrail-wis-form" method="post" hidden
                          data-ln-error-message="<?= htmlspecialchars(
                              tr('powertrail_admin_change_percent_required_error'),
                              ENT_QUOTES,
                              'UTF-8'
                          ); ?>"
                          action="<?= SimpleRouter::getLink(
                              'Admin.PowerTrailAdmin',
                              'changePerccentRequired',
                              (int) $powerTrail['id']
                          ); ?>">
                        <span class="d-flex align-items-center">
                            <input type="number" name="wis" class="form-control w-25 mr-10"
                                   min="<?= (int) $view->wisMin; ?>"
                                   max="<?= (int) $view->wisMax; ?>"
                                   step="1" required
                                   value="<?= (int) $powerTrail['perccentRequired']; ?>">
                            <button type="submit" class="blue-button-small"><?= tr('save'); ?></button>
                        </span>
                    </form>
                </td>
            </tr>
            <tr>
                <td class="content-title-noshade"><?= tr('date_created_label'); ?></td>
                <td class="powerTrail-date-created">
                    <span class="powerTrail-date-created-label mr-10"><?= htmlspecialchars($dateCreatedDisplay, ENT_QUOTES, 'UTF-8'); ?></span>
                    <button type="button" class="blue-button-small powerTrail-date-created-change-button">
                        <?= tr('pt045'); ?>
                    </button>
                    <form class="powerTrail-date-created-form" method="post" hidden
                          data-ln-error-message="<?= htmlspecialchars(
                              tr('powertrail_admin_change_date_error'),
                              ENT_QUOTES,
                              'UTF-8'
                          ); ?>"
                          action="<?= SimpleRouter::getLink(
                              'Admin.PowerTrailAdmin',
                              'changeDateCreated',
                              (int) $powerTrail['id']
                          ); ?>">
                        <span class="d-flex align-items-center">
                            <input type="date" name="dateCreated" class="form-control mr-10"
                                   value="<?= htmlspecialchars($dateCreatedInputValue, ENT_QUOTES, 'UTF-8'); ?>"
                                   required>
                            <button type="submit" class="blue-button-small"><?= tr('save'); ?></button>
                        </span>
                    </form>
                </td>
            </tr>
            <tr>
                <td class="content-title-noshade"><?= tr('pt033'); ?></td>
                <td>
                    <form method="post"
                          action="<?= SimpleRouter::getLink(
                              'Admin.PowerTrailAdmin',
                              'refreshPowerTrail',
                              (int) $powerTrail['id']
                          ); ?>">
                        <button type="submit" class="blue-button-small"><?= tr('pt033'); ?></button>
                    </form>
                </td>
            </tr>
            <tr>
                <td class="content-title-noshade"><?= tr('powertrail_admin_founders'); ?></td>
                <td class="powerTrail-founders"
                    data-remove-action="<?= SimpleRouter::getLink(
                        'Admin.PowerTrailAdmin',
                        'removeFounder',
                        (int) $powerTrail['id']
                    ); ?>"
                    data-ln-confirm-message="<?= htmlspecialchars(tr('powertrail_admin_confirm_remove_founder'), ENT_QUOTES, 'UTF-8'); ?>"
                    data-ln-remove-error="<?= htmlspecialchars(tr('powertrail_admin_remove_founder_error'), ENT_QUOTES, 'UTF-8'); ?>"
                    data-ln-remove-label="<?= htmlspecialchars(tr('powertrail_admin_remove'), ENT_QUOTES, 'UTF-8'); ?>"
                    data-ln-no-founders="<?= htmlspecialchars(tr('powertrail_admin_no_founders'), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="powerTrail-founders-list d-inline-flex flex-wrap align-items-center mr-10">
                            <?php if (empty($view->founders)) { ?>
                                <span class="powerTrail-founders-empty"><?= tr('powertrail_admin_no_founders'); ?></span>
                            <?php } ?>
                            <?php foreach ($view->founders as $founder) { ?>
                                <form class="powerTrail-founder powerTrail-remove-founder-form d-inline-flex align-items-center mr-5"
                                      method="post"
                                      data-ln-confirm-message="<?= htmlspecialchars(tr('powertrail_admin_confirm_remove_founder'), ENT_QUOTES, 'UTF-8'); ?>"
                                      data-ln-error-message="<?= htmlspecialchars(tr('powertrail_admin_remove_founder_error'), ENT_QUOTES, 'UTF-8'); ?>"
                                      action="<?= SimpleRouter::getLink(
                                          'Admin.PowerTrailAdmin',
                                          'removeFounder',
                                          (int) $powerTrail['id']
                                      ); ?>">
                                    <a href="/viewprofile.php?userid=<?= (int) $founder['user_id']; ?>"
                                       target="_blank" rel="noopener noreferrer">
                                        <?= htmlspecialchars((string) $founder['username'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                    <input type="hidden" name="userId" value="<?= (int) $founder['user_id']; ?>">
                                    <button type="submit" class="red-button-small px-5 ml-5 powerTrail-founder-remove"
                                            title="<?= htmlspecialchars(tr('powertrail_admin_remove'), ENT_QUOTES, 'UTF-8'); ?>"
                                            hidden>&times;</button>
                                </form>
                            <?php } ?>
                        </div>
                        <button type="button" class="blue-button-small powerTrail-founders-edit-button">
                            <?= tr('powertrail_admin_edit_founders'); ?>
                        </button>
                    </div>
                    <form class="powerTrail-add-founder-form mt-2" method="post" hidden
                          data-ln-error-message="<?= htmlspecialchars(tr('powertrail_admin_add_founder_error'), ENT_QUOTES, 'UTF-8'); ?>"
                          action="<?= SimpleRouter::getLink(
                              'Admin.PowerTrailAdmin',
                              'addFounder',
                              (int) $powerTrail['id']
                          ); ?>">
                        <span class="d-flex align-items-center">
                            <input type="text" name="username" class="form-control w-25 mr-10"
                                   placeholder="<?= htmlspecialchars(tr('powertrail_admin_founder_name_placeholder'), ENT_QUOTES, 'UTF-8'); ?>"
                                   maxlength="60" required>
                            <button type="submit" class="blue-button-small">
                                <?= tr('powertrail_admin_add_owner_to_geopath'); ?>
                            </button>
                        </span>
                        <div class="powerTrail-founders-message mt-2" role="status" aria-live="polite"></div>
                    </form>
                    <div class="mt-2">
                        <button type="button" class="blue-button-small powerTrail-founders-finish-button" hidden>
                            <?= tr('powertrail_admin_finish_editing_founders'); ?>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="content-title-noshade"><?= tr('powertrail_admin_view_geopath'); ?></td>
                <td>
                    <a href="/powerTrail.php?ptAction=showSerie&amp;ptrail=<?= (int) $powerTrail['id']; ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="blue-button-small">
                        <?= tr('powertrail_admin_view_geopath'); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td class="content-title-noshade"><?= tr('pt034'); ?></td>
                <td class="powerTrail-description"
                    data-description-target="powerTrailDescriptionEditorContainer">
                    <button type="button" class="blue-button-small powerTrail-description-edit-button">
                        <?= tr('powertrail_admin_edit_description'); ?>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>

    <div id="powerTrailDescriptionEditorContainer" class="powerTrail-description-editor w-100">
        <form class="powerTrail-description-form w-100" method="post"
              data-ln-error-message="<?= htmlspecialchars(
                  tr('powertrail_admin_description_error'),
                  ENT_QUOTES,
                  'UTF-8'
              ); ?>"
              action="<?= SimpleRouter::getLink(
                  'Admin.PowerTrailAdmin',
                  'updateDescription',
                  (int) $powerTrail['id']
              ); ?>">
            <div class="powerTrail-description-panel w-100" aria-hidden="true">
                <textarea id="powerTrailDescriptionEditor"
                          class="tinymce w-100"
                          style="height: 350px"><?= $descriptionForEditor; ?></textarea>
                <input type="hidden" name="description" value="<?= $descriptionForEditor; ?>">
                <div class="d-flex align-items-center mt-10">
                    <button type="submit"
                            class="blue-button-small powerTrail-description-save-button">
                        <?= tr('save'); ?>
                    </button>
                    <button type="button"
                            class="blue-button-small ml-10 powerTrail-description-hide-button">
                        <?= tr('powertrail_admin_hide_description_editor'); ?>
                    </button>
                    <span class="powerTrail-description-message ml-10"
                          role="status"
                          aria-live="polite"></span>
                </div>
            </div>
        </form>
    </div>

    <h2><?= tr('pt020'); ?></h2>
    <?php if (empty($view->caches)) { ?>
        <p><?= tr('powertrail_admin_no_caches'); ?></p>
    <?php } else { ?>
        <table class="table table-striped full-width">
            <thead>
                <tr>
                    <th><?= tr('waypoint'); ?></th>
                    <th><?= tr('name_label'); ?></th>
                    <th><?= tr('owner'); ?></th>
                    <th><?= tr('number_founds'); ?></th>
                    <th><?= tr('status_label'); ?></th>
                    <th class="text-center"><?= tr('actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($view->caches as $cache) { ?>
                    <tr>
                        <td class="align-middle">
                            <?php $waypoint = (string) ($cache['wp_oc'] ?? ''); ?>
                            <a href="/viewcache.php?wp=<?= rawurlencode($waypoint); ?>">
                                <?= htmlspecialchars($waypoint, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </td>
                        <td class="align-middle">
                            <a href="/viewcache.php?cacheid=<?= (int) $cache['cache_id']; ?>">
                                <?= htmlspecialchars($cache['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </td>
                        <td class="align-middle">
                            <?php if (! empty($cache['user_id']) && ! empty($cache['username'])) { ?>
                                <a href="/viewprofile.php?userid=<?= (int) $cache['user_id']; ?>"
                                   target="_blank" rel="noopener noreferrer">
                                    <?= htmlspecialchars($cache['username'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            <?php } else { ?>
                                <?= htmlspecialchars($cache['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            <?php } ?>
                        </td>
                        <td class="align-middle"><?= (int) $cache['founds']; ?></td>
                        <td class="align-middle">
                            <?= htmlspecialchars(
                                $view->cacheStatuses[(int) $cache['status']] ?? (string) $cache['status'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </td>
                        <td class="text-center align-middle">
                            <form class="powerTrail-remove-cache-form" method="post"
                                  data-ln-confirm-message="<?= htmlspecialchars(tr('powertrail_admin_confirm_remove_cache'), ENT_QUOTES, 'UTF-8'); ?>"
                                  data-ln-error-message="<?= htmlspecialchars(tr('powertrail_admin_remove_cache_error'), ENT_QUOTES, 'UTF-8'); ?>"
                                  action="<?= SimpleRouter::getLink(
                                      'Admin.PowerTrailAdmin',
                                      'removeCache',
                                      (int) $powerTrail['id']
                                  ); ?>">
                                <input type="hidden" name="cacheId" value="<?= (int) $cache['cache_id']; ?>">
                                <button type="submit" class="blue-button-small"><?= tr('powertrail_admin_remove'); ?></button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>
