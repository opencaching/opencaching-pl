<?php

namespace src\Models\PowerTrail;

use src\Models\User\User;

class Owner extends User
{
    private $privileges;

    /**
     * @return mixed
     */
    public function getPrivileges()
    {
        return $this->privileges;
    }

    /**
     * @param mixed $privileges
     */
    public function setPrivileges($privileges): Owner
    {
        $this->privileges = $privileges;

        return $this;
    }
}
