<?php

final class UserCollection
{
    private array $userArray = [];

    private function __construct()
    {
        include __DIR__ . '/../lib/settingsGlue.inc.php';

        if (isset($userCollection)) {
            $this->userArray = $userCollection;
        }
    }

    public static function Instance(): UserCollection
    {
        static $inst = null;

        if ($inst === null) {
            $inst = new UserCollection();
        }

        return $inst;
    }

    public function getUserCollection(): array
    {
        return $this->userArray;
    }

}
