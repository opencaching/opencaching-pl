<?php

declare(strict_types=1);

namespace src\Controllers\Core;

use src\Utils\Uri\HttpCode;

/**
 * Guard for controllers (extending ViewBaseController) whose code needs a newer PHP than the
 * oldest one supported by the application, e.g. controllers using enums or readonly properties.
 *
 * Call requireMinPhpVersion() as the FIRST thing in the constructor - before any class that
 * uses newer syntax is loaded or instantiated - otherwise PHP fails with a fatal error.
 *
 * IMPORTANT: this file and the controller using it must stay parsable by the oldest supported
 * PHP (7.4), so do not use PHP 8+ syntax here.
 */
trait RequiresMinPhpVersionTrait
{
    /**
     * Stops the request with an error page when the running PHP is older than required.
     *
     * @param int $minVersionId minimal PHP version as PHP_VERSION_ID, e.g. 80100 for PHP 8.1
     */
    protected function requireMinPhpVersion(int $minVersionId = 80100): void
    {
        if (PHP_VERSION_ID >= $minVersionId) {
            return;
        }

        $this->displayCommonErrorPageAndExit(
            'This module requires newer PHP version (current: ' . PHP_VERSION . ')',
            HttpCode::STATUS_INTERNAL_ERROR
        );
    }
}
