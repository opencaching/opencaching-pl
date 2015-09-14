<?php

$rootpath = '../';

class MakeLogo
{

    function run()
    {
        global $dynbasepath, $rootpath;
        $MAX_LEN = 630;

        $totalWidth = 0;
        $counter = 0;
        $im = imagecreatetruecolor($MAX_LEN, 80);
        $alpha = imagecolorallocate($im, 255, 255, 255);
        imagefilledrectangle($im, 0, 0, 800, 80, $alpha);
        imagecolortransparent($im, $alpha);
        $numbers = $this->mrand(1, 50, 50);

        while ($totalWidth < $MAX_LEN && $counter < 10) {
            $number = $numbers[$counter];
            $path = $rootpath . 'images/header/header_' . $number . '.jpg';
            $tmp = imagecreatefromjpeg($path);
            $totalWidth += imagesx($tmp);
            /*  echo "total=".$totalWidth;
              echo "<br />";
              echo "im_".$number."=".imagesx($tmp); */
            if ($totalWidth >= $MAX_LEN) {
                // return the real width od final logo
                $totalWidth -= imagesx($tmp);
                break;
            }
            imagecopy($im, $tmp, $totalWidth - imagesx($tmp), 0, 0, 0, imagesx($tmp), imagesy($tmp));
            $counter++;
        }
        $im_final = imagecreatetruecolor($totalWidth, 80);
        imagecopy($im_final, $im, 0, 0, 0, 0, $totalWidth, 80);
        imagejpeg($im_final, $rootpath . "images/header/logo.jpg", 90);
        imagedestroy($im);
        imagedestroy($im_final);
    }

    # Multiple Unique Random Numbers
    #
    # array mrand ( int min, int max, int count [, int strlen ] )

    function mrand($l, $h, $t, $len = false)
    {
        if ($l > $h) {
            $a = $l;
            $b = $h;
            $h = $a;
            $l = $b;
        }
        if ((($h - $l) + 1) < $t || $t <= 0)
            return false;

        $n = array();

        if ($len > 0) {

            if (strlen($h) < $len && strlen($l) < $len)
                return false;
            if (strlen($h - 1) < $len && strlen($l - 1) < $len && $t > 1)
                return false;

            do {
                $x = rand($l, $h);
                if (!in_array($x, $n) && strlen($x) == $len)
                    $n[] = $x;
            }
            while (count($n) < $t);
        }
        else {
            do {
                $x = rand($l, $h);
                if (!in_array($x, $n))
                    $n[] = $x;
            }
            while (count($n) < $t);
        }
        return $n;
    }

}

$makeLogo = new MakeLogo();
$makeLogo->run();
?>
