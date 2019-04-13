<?php
namespace src\Utils\Img;

use src\Utils\System\PhpInfo;
use src\Utils\Debug\Debug;

/**
 *
 * This class may be used to perform simple operation on image
 */
class OcImage
{
    const DEFAULT_JPEG_COMPRESSION = 75;

    private $gdImage = null;
    private $gdImageType = null;

    /**
     * Fast track for thumbnail creation
     * Extension to the $outputFile will be added automatically
     *
     * @param string $inputFile - path to the input image (with filename)
     * @param string $outputFile - path to the output image (with filename without extension)
     * @param array $maxSize - array of max dimensions
     * @return
     */
    public static function createThumbnail($inputFile, $outputFile, array $maxSize)
    {
        $img = new OcImage($inputFile);
        $img->resizeToMaxDimensions($maxSize);
        return $img->save($outputFile, true);
    }

    public function __construct($inputImagePath)
    {
        $this->load($inputImagePath);
    }

    /**
     * @return boolean - true if this image is an portrait
     */
    public function isPortrait()
    {
        return $this->getHeight() > $this->getWidth();
    }

    /**
     * @return int - resturn current width of the image
     */
    public function getWidth()
    {
        return @imagesx($this->gdImage);
    }

    /**
     * @return int - resturn current height of the image
     */
    public function getHeight()
    {
        return @imagesy($this->gdImage);
    }

    /**
     * @return int - return the recognized type of the image: IMAGETYPE_*
     */
    public function getType()
    {
        return $this->gdImageType;
    }

    private function load($inputImagePath)
    {
        // check if input file exists
        if (!is_file($inputImagePath)){
            throw new \Exception("Image not found: $inputImagePath");
        }

        // load img type
        $this->loadType($inputImagePath);

        if(!PhpInfo::versionAtLeast(7,2) && $this->gdImageType == IMAGETYPE_BMP){
            // GD doens't support BMP import before PHP 7.2
            $this->gdImage = $this->loadBmp($inputImagePath);
        } else {
            // open img
            $this->gdImage = @imagecreatefromstring(file_get_contents($inputImagePath));
        }

        if ($this->gdImage === false) {
            throw new \Exception("Can't open image - unsupported format.");
        }
    }

    /**
     * Resize the image to be sure this fit to max-dimensions
     * @param int $maxWidth     *
     */
    public function resizeToMaxDimensions(array $maxDimensions)
    {
        list($maxWidth, $maxHeight) = $maxDimensions;

        if ($this->getWidth() > $maxWidth) {
            // width is too high - resize
            $this->scale($maxWidth / $this->getWidth());
        }

        if ($this->getHeight() > $maxHeight) {
            $this->scale($maxHeight / $this->getHeight());
        }
    }

    /**
     * Scale the image by given ration
     * @param float $ratio
     */
    public function scale($ratio)
    {
        $this->resize($this->getWidth() * $ratio, $this->getHeight() * $ratio);
    }

    /**
     * Resize to given new width/height
     *
     * @param int $width
     * @param int $height
     * @throws \Exception
     */
    public function resize($width, $height)
    {
        // prepare new image
        $newImage = @imagecreatetruecolor($width, $height);
        if (!$newImage) {
            throw new \Exception("Can't create new image");
        }

        if (!@imagecopyresampled($newImage, $this->gdImage, 0, 0, 0, 0,
                $width, $height, $this->getWidth(), $this->getHeight())) {

            throw new \Exception("Can't resample new image");
        }

        $this->gdImage = $newImage;
    }

    /**
     * Crop an image to given params:
     *
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @throws \Exception
     */
    public function crop($x, $y, $width, $height)
    {
        $this->gdImage = @imagecrop($this->gdImage, [$x, $y, $width, $height] );
        if (!$this->gdImage) {
            throw new \Exception("Can't crop the image");
        }
    }

    /**
     * Rotate image by given degrees number
     *
     * @param int $degrees - degrees number <0-360>
     * @throws \Exception
     */
    public function rotate($degrees)
    {
        $this->gdImage = @imagerotate ($this->gdImage, $degrees, 0);
        if (!$this->gdImage) {
            throw new \Exception("Can't rotate the image");
        }
    }

    /**
     * Save an image under given filename.
     * Use same format as original image for GIFs and PNGs or JPEG of others
     *
     * @param string $outputPath - path to save the file
     * @param boolean $overwrite - overwrite the file if exists
     * @throws \Exception
     */
    public function save($outputPath, $overwrite=false)
    {
        if (!$overwrite && is_file($outputPath)) {
            throw new \Exception("Can't save - file already exists!");
        }

        if (!is_dir(dirname($outputPath))) {
            // there is no such dir - try to create it
            $dir = dirname($outputPath);
            if ( ! mkdir($dir, 0755, true)) {
                throw new \Exception("Can't save - there is no such directory and can't create it!");
            }
        }

        // there are only three possiblem outputs
        switch ($this->gdImageType) {
            case IMAGETYPE_PNG:
                $outputPath .= ".png";
                $result = imagepng($this->gdImage, $outputPath);
                break;
            case IMAGETYPE_GIF:
                $outputPath .= ".gif";
                $result = imagegif($this->gdImage, $outputPath);
                break;
            case IMAGETYPE_JPEG:
            default:
                $outputPath .= ".jpg";
                $result = imagejpeg($this->gdImage, $outputPath, self::DEFAULT_JPEG_COMPRESSION);
        }

        if(!$result){
            throw new \Exception("Can't save the output file: $outputPath");
        }

        return $outputPath;
    }

    private function loadType($inputImagePath)
    {
        if($imgParams = @getimagesize($inputImagePath)) {
            $this->gdImageType =  $imgParams[2];
        } else {
            throw new \Exception("Can't read image type?");
        }
    }

    /**
     * This function is necessary only for PHP<7.2 because of no support of BMP in GD
     * This code based on comment here:
     * http://php.net/manual/en/function.imagecreate.php#53879
     * greetings to author: DHKold admin at dhkold.com
     *
     * @param string $inputImagePath
     *            - path to the file
     * @return boolean|resource
     */
    private function loadBmp($inputImagePath)
    {
        if (! $fileHandler = fopen($inputImagePath, "rb")) {
            return false;
        }

        // 1 : read BMPFile header
        $fileHeader = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($fileHandler, 14));
        if ($fileHeader['file_type'] != "BM") {
            return false;
        }

        // 2 : read BMP header
        $bmp = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' . '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($fileHandler, 40));

        $bmp['colors'] = pow(2, $bmp['bits_per_pixel']);
        if ($bmp['size_bitmap'] == 0)
            $bmp['size_bitmap'] = $fileHeader['file_size'] - $fileHeader['bitmap_offset'];
        $bmp['bytes_per_pixel'] = $bmp['bits_per_pixel'] / 8;
        $bmp['bytes_per_pixel2'] = ceil($bmp['bytes_per_pixel']);
        $bmp['decal'] = ($bmp['width'] * $bmp['bytes_per_pixel'] / 4);
        $bmp['decal'] -= floor($bmp['width'] * $bmp['bytes_per_pixel'] / 4);
        $bmp['decal'] = 4 - (4 * $bmp['decal']);
        if ($bmp['decal'] == 4) {
            $bmp['decal'] = 0;
        }

        // 3 : palette decoding
        $palette = array();
        if ($bmp['colors'] < 0x1000000) {
            $palette = unpack('V' . $bmp['colors'], fread($fileHandler, $bmp['colors'] * 4));
        }

        // 4 : Image creation
        $img = fread($fileHandler, $bmp['size_bitmap']);
        $vide = chr(0);

        $res = imagecreatetruecolor($bmp['width'], $bmp['height']);
        $p = 0;
        $y = $bmp['height'] - 1;
        while ($y >= 0) {
            $x = 0;
            while ($x < $bmp['width']) {
                if ($bmp['bits_per_pixel'] == 24)
                    $color = unpack("V", substr($img, $p, 3) . $vide);
                elseif ($bmp['bits_per_pixel'] == 16) {
                    $color = unpack("n", substr($img, $p, 2));
                    $color[1] = $palette[$color[1] + 1];
                } elseif ($bmp['bits_per_pixel'] == 8) {
                    $color = unpack("n", $vide . substr($img, $p, 1));
                    $color[1] = $palette[$color[1] + 1];
                } elseif ($bmp['bits_per_pixel'] == 4) {
                    $color = unpack("n", $vide . substr($img, floor($p), 1));
                    if (($p * 2) % 2 == 0) {
                        $color[1] = ($color[1] >> 4);
                    } else {
                        $color[1] = ($color[1] & 0x0F);
                    }
                    $color[1] = $palette[$color[1] + 1];
                } elseif ($bmp['bits_per_pixel'] == 1) {
                    $color = unpack("n", $vide . substr($img, floor($p), 1));
                    if (($p * 8) % 8 == 0)
                        $color[1] = $color[1] >> 7;
                    elseif (($p * 8) % 8 == 1)
                        $color[1] = ($color[1] & 0x40) >> 6;
                    elseif (($p * 8) % 8 == 2)
                        $color[1] = ($color[1] & 0x20) >> 5;
                    elseif (($p * 8) % 8 == 3)
                        $color[1] = ($color[1] & 0x10) >> 4;
                    elseif (($p * 8) % 8 == 4)
                        $color[1] = ($color[1] & 0x8) >> 3;
                    elseif (($p * 8) % 8 == 5)
                        $color[1] = ($color[1] & 0x4) >> 2;
                    elseif (($p * 8) % 8 == 6)
                        $color[1] = ($color[1] & 0x2) >> 1;
                    elseif (($p * 8) % 8 == 7)
                        $color[1] = ($color[1] & 0x1);
                    $color[1] = $palette[$color[1] + 1];
                } else
                    return false;
                imagesetpixel($res, $x, $y, $color[1]);
                $x ++;
                $p += $bmp['bytes_per_pixel'];
            }
            $y --;
            $p += $bmp['decal'];
        }

        // Fermeture du fichier
        fclose($fileHandler);

        return $res;
    }
}
