<?php

use Utils\Database\OcDb;


function getSmallCacheIcon($iconname)
{
    $iconname = mb_eregi_replace('([^/]+)$', '16x16-\1', $iconname);
    return $iconname;
}


