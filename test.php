<?php
error_reporting(-1);
require __DIR__.'/lib/ClassPathDictionary.php';
spl_autoload_register(function ($className) {
    include_once ClassPathDictionary::getClassPath($className);
});

$user_id = 9067;
$rs=dataBase::select(array('latitude', 'longitude'), 'user', array(0 => array ('fieldName' => 'user_id', 'fieldValue' => $user_id, 'operator' => '=')));

var_dump($rs);