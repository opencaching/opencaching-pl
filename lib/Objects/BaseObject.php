<?php
namespace lib\Objects;

class BaseObject
{

    protected $dataLoaded = false; //are data loaded to this object


    public function isDataLoaded()
    {
        return $this->dataLoaded;
    }
}
