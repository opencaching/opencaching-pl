<?php
namespace src\Models\Pictures;

use src\Models\BaseObject;
use src\Models\OcConfig\OcConfig;
use src\Models\User\User;
use src\Utils\Uri\Uri;

/**
 * Generic representation of user statistic banner
 *
 */

class StatPic extends BaseObject
{
    private $id;
    private $tplPath;
    private $previewPath;
    private $description;
    private $maxTxtWidth;

    public static function getStatPicUrl($userId)
    {
        return '/images/statpics/statpic'.$userId.'.jpg';
    }

    private static function getStatPicPath($userId)
    {
        return OcConfig::getDynFilesPath(true) . self::getStatPicUrl($userId);
    }

    public static function isStatPicPresent ($userId)
    {
        return file_exists (self::getStatPicPath($userId));
    }

    /**
     * Get object with statPic templ. decription based on tpl-Id
     * @param int $tplId
     *
     * @return StatPic | null
     */
    public static function fromTplIdFactory($tplId)
    {
        $db = self::db();
        $row = $db->dbResultFetchOneRowOnly(
            $db->multiVariableQuery('SELECT * FROM statpics WHERE id = :1 LIMIT 1', $tplId));

        if (empty($row)) {
            return null;
        }

        $obj = new self();
        $obj->loadFromDbRow($row);
        return $obj;
    }

    public static function getDefaultTpl()
    {
        return self::fromTplIdFactory(1);
    }

    public static function getAllTpls ()
    {
        $db = self::db();
        return $db->dbFetchAllAsObjects(
            $db->simpleQuery('SELECT * FROM statpics'),
            function ($row) {
                $obj = new self();
                $obj->loadFromDbRow($row);
                return $obj;
            });
    }

    /**
     * Generate statPic banner for given user
     * @param int $userId
     */
    public static function generateStatPic(User $user)
    {

        // find user template
        list ($statPicText, $statPicLogo) = $user->getStatPicDataArr();
        $statPicTpl = self::fromTplIdFactory($statPicLogo);
        if (is_null($statPicTpl)) {
            // there is no such tpl - take the defaut one
            $statPicTpl = self::getDefaultTpl();
        }

        $im = ImageCreateFromGIF (Uri::getAbsServerPath('/'.$statPicTpl->tplPath));
        $maxTxtWidth = $statPicTpl->maxtextwidth;

        $clrBlack = ImageColorAllocate($im, 0, 0, 0);

        $found = $user->getFoundGeocachesCount();
        $hidden = $user->getHiddenGeocachesCount();

        $fontfile = Uri::getAbsServerPath("/resources/fonts/arial.ttf");

        switch ($statPicTpl->getId()) {
            case 4:
            case 5:
                $fontSz = 10;
                $text = $user->getUserName();
                $txtSz = imagettfbbox($fontSz, 0, $fontfile, $text);
                ImageTTFText(
                    $im, $fontSz, 0,
                    max (imagesx($im) - ($txtSz[2] - $txtSz[0]) - 8, $maxTxtWidth),
                    15, $clrBlack, $fontfile, $text);

                $fontSz = 8;
                $text = tr('statpic_found') . $found . ' / ' . tr('statpic_hidden') . $hidden;
                $txtSz = imagettfbbox($fontSz, 0, $fontfile, $text);
                ImageTTFText($im, $fontSz, 0,
                    max (imagesx($im) - ($txtSz[2] - $txtSz[0]) - 8, $maxTxtWidth),
                    32, $clrBlack, $fontfile, $text);

                break;
            case 2:
                $fontSz = 10;
                $text = $user->getUserName();
                $txtSz = imagettfbbox($fontSz, 0, $fontfile, $text);
                ImageTTFText($im, $fontSz, 0,
                    max (imagesx($im) - ($txtSz[2] - $txtSz[0]) - 8, $maxTxtWidth),
                    15, $clrBlack, $fontfile, $text);

                $fontSz = 7;
                $txtSz = imagettfbbox($fontSz, 0, $fontfile, $statPicText);
                ImageTTFText($im, $fontSz, 0,
                    max (imagesx($im) - ($txtSz[2] - $txtSz[0]) - 5, $maxTxtWidth),
                    29, $clrBlack, $fontfile, $statPicText);

                $fontSz = 8;
                $text = tr('statpic_found') . $found . ' / ' . tr('statpic_hidden') . $hidden;
                $txtSz = imagettfbbox($fontSz, 0, $fontfile, $text);
                ImageTTFText($im, $fontSz, 0,
                    max (imagesx($im) - ($txtSz[2] - $txtSz[0]) - 8, $maxTxtWidth),
                    45, $clrBlack, $fontfile, $text);

                break;
            case 6:
            case 7:
                $fontSz = 10;
                $text = $user->getUserName();
                $txtSz = imagettfbbox($fontSz, 0, $fontfile, $text);
                ImageTTFText($im, $fontSz, 0,
                    max (imagesx($im) - ($txtSz[2] - $txtSz[0]) - 5, $maxTxtWidth),
                    15, $clrBlack, $fontfile, $text);

                $fontSz = 7.5;
                $txtSz = imagettfbbox($fontSz, 0, $fontfile, $statPicText);
                ImageTTFText($im, $fontSz, 0,
                    max (imagesx($im) - ($txtSz[2] - $txtSz[0]) - 5, $maxTxtWidth),
                    32, $clrBlack, $fontfile, $statPicText);

                break;
            case 1:
            default:
                $fontSz = 10;
                $text = $user->getUserName();
                $txtSz = imagettfbbox($fontSz, 0, $fontfile, $text);
                ImageTTFText($im, $fontSz, 0,
                    max (imagesx($im) - ($txtSz[2] - $txtSz[0]) - 5, $maxTxtWidth),
                    15, $clrBlack, $fontfile, $text);

                $fontSz = 8;
                $text = tr('statpic_found') . $found . ' / ' . tr('statpic_hidden') . $hidden;
                $txtSz = imagettfbbox($fontSz, 0, $fontfile, $text);
                ImageTTFText($im, $fontSz, 0,
                    max (imagesx($im) - ($txtSz[2] - $txtSz[0]) - 5, $maxTxtWidth),
                    29, $clrBlack, $fontfile, $text);

                $fontSz = 8;
                $txtSz = imagettfbbox($fontSz, 0, $fontfile, $statPicText);
                ImageTTFText($im, $fontSz, 0,
                    max (imagesx($im) - ($txtSz[2] - $txtSz[0]) - 5, $maxTxtWidth),
                    45, $clrBlack, $fontfile, $statPicText);

        } // switch (statPic-TPL)

        // draw border
        ImageRectangle($im, 0, 0, imagesx($im) - 1, imagesy($im) - 1, ImageColorAllocate($im, 70, 70, 70));

        // write output
        Imagejpeg($im, self::getStatPicPath ($user->getUserId()), 80);
        ImageDestroy($im);
    }

    protected function loadFromDbRow($row)
    {
        $this->id = $row['id'];
        $this->tplPath = $row ['tplpath'];
        $this->previewPath = $row['previewpath'];
        $this->description = $row['description'];
        $this->maxtextwidth = $row['maxtextwidth'];
    }


    public function getId ()
    {
        return $this->id;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getPreviewPath ()
    {
        return $this->previewPath;
    }

}