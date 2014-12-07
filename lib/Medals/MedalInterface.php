<?php
namespace lib\Medals;

/**
 * Description of medalInterface
 *
 * @author Łza
 */
interface MedalInterface
{
    public function checkConditionsForUser(\lib\User\User $user);
}
