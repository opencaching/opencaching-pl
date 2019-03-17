<?php
namespace src\Utils\Img;

/**
 *
 * This class may be used to perform simple operation on image
 */
class OcImage
{
    const DEFAULT_JPEG_COMPRESSION = 75;

    private $gdImage = null;
    private $gdImageType = null;


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
     * @return unknown - return the recognized type of the image: IMAGETYPE_*
     */
    public function getType()
    {
        return $this->gdImageType;
    }

    private function load($inputImagePath)
    {
        // check if input file exists
        if (!is_file($inputImagePath)){
            throw new \Exception("Image not found");
        }

        // load img type
        $this->loadType($inputImagePath);

        // open img
        $this->gdImage = imagecreatefromstring(file_get_contents($inputImagePath));

        if ($this->gdImage === false) {
            throw new \Exception("Can't open image - unsupported format.");
        }
    }

    /**
     * Resize the image to be sure this fit to max-dimensions
     * @param int $maxWidth
     * @param int $maxHeight
     */
    public function resizeToMaxDimensions($maxWidth, $maxHeight)
    {
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
        if(!$overwrite && is_file($outputPath)){
            throw new \Exception("Can't save - file already exists!");
        }

        // there are only three possiblem outputs
        switch ($this->gdImageType) {
            case IMAGETYPE_PNG:
                $result = @imagecreatefrompng($this->gdImage, $outputPath);
                break;
            case IMAGETYPE_GIF:
                $result = @imagegif($this->gdImage, $outputPath);
                break;
            case IMAGETYPE_JPEG:
            default:
                $result = @imagecreatefrompng($this->gdImage, $outputPath, self::DEFAULT_JPEG_COMPRESSION);
        }

        if(!$result){
            throw new \Exception("Can't save the output file");
        }
    }

    private function loadType($inputImagePath)
    {
        if($imgParams = @getimagesize($inputImagePath)) {
            $this->gdImageType =  $imgParams[2];
        } else {
            throw new \Exception("Can't read image type?");
        }
    }
}
