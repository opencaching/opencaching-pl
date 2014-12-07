<?php
namespace tpl\stdstyle\lib;

/**
 * Description of medalsLayout
 *
 * @author Łza
 */
class MedalsLayout
{
    private $path = 'tpl/stdstyle/medals/';
    private $medalImages = array(
        1 => 'medal_1.png',
        2 => 'medal_2.png',
        3 => 'medal_3.png',
        4 => 'GDKJ.jpg',
    );

    public function getImage($medalType) {
        return $this->path.$this->medalImages[$medalType];
    }
}
