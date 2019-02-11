<?php

namespace src\Models\PowerTrail;

use src\Models\User\User;

class Owner extends User
{
    private $privileages;

    /**
     * @return mixed
     */
    public function getPrivileages()
    {
        return $this->privileages;
    }

    /**
     * @param mixed $privileages
     * @return Owner
     */
    public function setPrivileages($privileages)
    {
        $this->privileages = $privileages;
        return $this;
    }


}
