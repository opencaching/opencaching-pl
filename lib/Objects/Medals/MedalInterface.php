<?php

namespace lib\Objects\Medals;

/**
 * Description of medalInterface
 *
 * @author Łza
 */
interface MedalInterface
{
    public function checkConditionsForUser(\lib\Objects\User\User $user);
    public function getLevelInfo($level);
}
