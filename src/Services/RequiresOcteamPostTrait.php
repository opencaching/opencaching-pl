<?php

declare(strict_types=1);

namespace src\Services;

use RuntimeException;
use src\Models\ApplicationContainer;
use src\Models\User\User;

/**
 * Guard for operations that may be done only by an Octeam user via HTTP POST.
 */
trait RequiresOcteamPostTrait
{
    /**
     * @throws RuntimeException 405 when the request is not POST, 403 when the user is not Octeam
     */
    protected function requireOcteamPost(): User
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            throw new RuntimeException('POST required', 405);
        }

        $user = ApplicationContainer::GetAuthorizedUser();
        if (! $user?->hasOcTeamRole()) {
            throw new RuntimeException('Octeam access required', 403);
        }

        return $user;
    }
}
