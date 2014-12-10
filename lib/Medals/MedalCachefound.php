<?php

namespace lib\Medals;

/**
 * Description of medalGeographical
 *
 * @author Łza
 */
class MedalCachefound extends medal implements \lib\Medals\MedalInterface
{

    private $medalcachefoundTest = 'asdasdas';

    public function checkConditionsForUser(\lib\User\User $user)
    {
        d('add a body!!');
    }

}
