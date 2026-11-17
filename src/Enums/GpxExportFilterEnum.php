<?php

declare(strict_types=1);

namespace src\Enums;

/**
 * Filters available in the GPX export panel.
 */
enum GpxExportFilterEnum: string
{
    case INACTIVE = 'inactive';
    case IGNORED = 'ignored';
    case OWNED = 'owned';
    case WATCHED = 'watched';
    case FOUND = 'found';
}