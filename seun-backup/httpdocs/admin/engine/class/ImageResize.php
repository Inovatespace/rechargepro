<?php
//namespace Eventviva;
ini_set('memory_limit', '428M');
ini_set('max_execution_time', 5000);
//use \Exception;

/**
 * PHP class to resize and scale images
 */
class ImageResize extends timekeeper
{
    const CROPTOP = 1;
    const CROPCENTRE = 2;
    const CROPCENTER = 2;
    const CROPBOTTOM = 3;
    const CROPLEFT = 4;
    const CROPRIGHT = 5;

    public $quality_jpg = 100;
    public $quality_png = 0;

    public $interlace = 0;

    public $source_type;

    protected $source_image;

    protected $original_w;
    protected $original_h;

    protected $dest_x = 0;
    protected $dest_y = 0;

    protected $source_x;
    protected $source_y;

    protected $dest_w;
    protected $dest_h;

    protected $source_w;
    protected $source_h;

    /**
     * Create instance from a strng
     *
     * @param string $image_data
     * @return ImageResize
     * @throws \exception
     */
    public static function createFromString($image_data)
    {
        $resize = new self('data://application/octet-stream;base64,' . base64_encode($image_data));
        return $resize;
    }

    /**
     * Loads image source and its properties to the instanciated object
     *
     * @param string $filename
     * @return ImageResize
     * @throws \Exception
     */
    public function __construct($filename)
    {
        $image_info = @getimagesize($filename);

        if (!$image_info) {
            throw new \Exception('Could not read file');
        }

        list (
            $this->original_w,
            $this->original_h,
            $this->source_type
        ) = $image_info;

        switch ($this->source_type) {
            case IMAGETYPE_GIF:
                $this->source_image = imagecreatefromgif($filename);
                break;

            case IMAGETYPE_JPEG:
                $this->source_image = $this->imageCreateJpegfromExif($filename);

                // set new width and height for image, maybe it has changed
                $this->original_w = ImageSX($this->source_image);
                $this->original_h = ImageSY($this->source_image);

                break;

            case IMAGETYPE_PNG:
                $this->source_image = imagecreatefrompng($filename);
                break;

            default:
                throw new \Exception('Unsupported image type');
                break;
        }

        return $this->resize($this->getSourceWidth(), $this->getSourceHeight());
    }

    // http://stackoverflow.com/a/28819866
    public function imageCreateJpegfromExif($filename){
      $img = imagecreatefromjpeg($filename);
      $exif = @exif_read_data($filename);

      if (!$exif || !isset($exif['Orientation'])){
        return $img;
      }

      $orientation = $exif['Orientation'];

      if ($orientation === 6 || $orientation === 5){
        $img = imagerotate($img, 270, null);
      } else if ($orientation === 3 || $orientation === 4){
        $img = imagerotate($img, 180, null);
      } else if ($orientation === 8 || $orientation === 7){
        $img = imagerotate($img, 90, null);
      }

      if ($orientation === 5 || $orientation === 4 || $orientation === 7){
        imageflip($img, IMG_FLIP_HORIZONTAL);
      }

      return $img;
    }

    /**
     * Saves new image
     *
     * @param string $filename
     * @param string $image_type
     * @param integer $quality
     * @param integer $permissions
     * @return \static
     */
    public function save($filename, $image_type = null, $quality = null, $permissions = null)
    {


        $image_type = $image_type ?: $this->source_type;

        $dest_image = imagecreatetruecolor($this->getDestWidth(), $this->getDestHeight());

        imageinterlace($dest_image, $this->interlace);

        switch ($image_type) {
            case IMAGETYPE_GIF:
                $background = imagecolorallocatealpha($dest_image, 255, 255, 255, 1);
                imagecolortransparent($dest_image, $background);
                imagefill($dest_image, 0, 0 , $background);
                imagesavealpha($dest_image, true);
                break;

            case IMAGETYPE_JPEG:
                $background = imagecolorallocate($dest_image, 255, 255, 255);
                imagefilledrectangle($dest_image, 0, 0, $this->getDestWidth(), $this->getDestHeight(), $background);
                break;

            case IMAGETYPE_PNG:
                imagealphablending($dest_image, false);
                imagesavealpha($dest_image, true);
                break;
        }

        imagecopyresampled(
            $dest_image,
            $this->source_image,
            $this->dest_x,
            $this->dest_y,
            $this->source_x,
            $this->source_y,
            $this->getDestWidth(),
            $this->getDestHeight(),
            $this->source_w,
            $this->source_h
        );

        switch ($image_type) {
            case IMAGETYPE_GIF:
                imagegif($dest_image, $filename);
                break;

            case IMAGETYPE_JPEG:
                if ($quality === null) {
                    $quality = $this->quality_jpg;
                }

                imagejpeg($dest_image, $filename, $quality);
                break;

            case IMAGETYPE_PNG:
                if ($quality === null) {
                    $quality = $this->quality_png;
                }

                imagepng($dest_image, $filename, $quality);
                break;
        }

        if ($permissions) {
            chmod($filename, $permissions);
        }

        return $this;
    }

    /**
     * Convert the image to string
     *
     * @param int $image_type
     * @param int $quality
     * @return string
     */
    public function getImageAsString($image_type = null, $quality = null)
    {
        $string_temp = tempnam(sys_get_temp_dir(), '');

        $this->save($string_temp, $image_type, $quality);

        $string = file_get_contents($string_temp);

        unlink($string_temp);

        return $string;
    }

    /**
    * Convert the image to string with the current settings
    *
    * @return string
    */
    public function __toString()
    {
        return $this->getImageAsString();
    }

    /**
     * Outputs image to browser
     * @param string $image_type
     * @param integer $quality
     */
    public function output($image_type = null, $quality = null)
    {
        $image_type = $image_type ?: $this->source_type;

        header('Content-Type: ' . image_type_to_mime_type($image_type));

        $this->save(null, $image_type, $quality);
    }

    /**
     * Resizes image according to the given height (width proportional)
     *
     * @param integer $height
     * @param boolean $allow_enlarge
     * @return \static
     */
    public function resizeToHeight($height, $allow_enlarge = false)
    {
        $ratio = $height / $this->getSourceHeight();
        $width = $this->getSourceWidth() * $ratio;

        $this->resize($width, $height, $allow_enlarge);

        return $this;
    }

    /**
     * Resizes image according to the given width (height proportional)
     *
     * @param integer $width
     * @param boolean $allow_enlarge
     * @return \static
     */
    public function resizeToWidth($width, $allow_enlarge = false)
    {
        $ratio  = $width / $this->getSourceWidth();
        $height = $this->getSourceHeight() * $ratio;

        $this->resize($width, $height, $allow_enlarge);

        return $this;
    }

    /**
     * Resizes image to best fit inside the given dimensions
     *
     * @param integer $max_width
     * @param integer $max_height
     * @param boolean $allow_enlarge
     * @return \static
     */
    public function resizeToBestFit($max_width, $max_height, $allow_enlarge = false)
    {
        if($this->getSourceWidth() <= $max_width && $this->getSourceHeight() <= $max_height && $allow_enlarge === false){
            return $this;
        }

        $ratio  = $this->getSourceHeight() / $this->getSourceWidth();
        $width = $max_width;
        $height = $width * $ratio;

        if($height > $max_height){
            $height = $max_height;
            $width = $height / $ratio;
        }

        return $this->resize($width, $height, $allow_enlarge);
    }

    /**
     * Resizes image according to given scale (proportionally)
     *
     * @param integer|float $scale
     * @return \Eventviva\ImageResize
     */
    public function scale($scale)
    {
        $width  = $this->getSourceWidth() * $scale / 100;
        $height = $this->getSourceHeight() * $scale / 100;

        $this->resize($width, $height, true);

        return $this;
    }

    /**
     * Resizes image according to the given width and height
     *
     * @param integer $width
     * @param integer $height
     * @param boolean $allow_enlarge
     * @return \static
     */
    public function resize($width, $height, $allow_enlarge = false)
    {
        if (!$allow_enlarge) {
            // if the user hasn't explicitly allowed enlarging,
            // but either of the dimensions are larger then the original,
            // then just use original dimensions - this logic may need rethinking

            if ($width > $this->getSourceWidth() || $height > $this->getSourceHeight()) {
                $width  = $this->getSourceWidth();
                $height = $this->getSourceHeight();
            }
        }

        $this->source_x = 0;
        $this->source_y = 0;

        $this->dest_w = $width;
        $this->dest_h = $height;

        $this->source_w = $this->getSourceWidth();
        $this->source_h = $this->getSourceHeight();

        return $this;
    }

    /**
     * Crops image according to the given width, height and crop position
     *
     * @param integer $width
     * @param integer $height
     * @param boolean $allow_enlarge
     * @param integer $position
     * @return \static
     */
    public function crop($width, $height, $allow_enlarge = false, $position = self::CROPCENTER)
    {
        if (!$allow_enlarge) {
            // this logic is slightly different to resize(),
            // it will only reset dimensions to the original
            // if that particular dimenstion is larger

            if ($width > $this->getSourceWidth()) {
                $width  = $this->getSourceWidth();
            }

            if ($height > $this->getSourceHeight()) {
                $height = $this->getSourceHeight();
            }
        }

        $ratio_source = $this->getSourceWidth() / $this->getSourceHeight();
        $ratio_dest = $width / $height;

        if ($ratio_dest < $ratio_source) {
            $this->resizeToHeight($height, $allow_enlarge);

            $excess_width = ($this->getDestWidth() - $width) / $this->getDestWidth() * $this->getSourceWidth();

            $this->source_w = $this->getSourceWidth() - $excess_width;
            $this->source_x = $this->getCropPosition($excess_width, $position);

            $this->dest_w = $width;
        } else {
            $this->resizeToWidth($width, $allow_enlarge);

            $excess_height = ($this->getDestHeight() - $height) / $this->getDestHeight() * $this->getSourceHeight();

            $this->source_h = $this->getSourceHeight() - $excess_height;
            $this->source_y = $this->getCropPosition($excess_height, $position);

            $this->dest_h = $height;
        }

        return $this;
    }

    /**
     * Gets source width
     *
     * @return integer
     */
    public function getSourceWidth()
    {
        return $this->original_w;
    }

    /**
     * Gets source height
     *
     * @return integer
     */
    public function getSourceHeight()
    {
        return $this->original_h;
    }

    /**
     * Gets width of the destination image
     *
     * @return integer
     */
    public function getDestWidth()
    {
        return $this->dest_w;
    }

    /**
     * Gets height of the destination image
     * @return integer
     */
    public function getDestHeight()
    {
        return $this->dest_h;
    }

    /**
     * Gets crop position (X or Y) according to the given position
     *
     * @param integer $expectedSize
     * @param integer $position
     * @return integer
     */
    protected function getCropPosition($expectedSize, $position = self::CROPCENTER)
    {
        $size = 0;
        switch ($position) {
            case self::CROPBOTTOM:
            case self::CROPRIGHT:
                $size = $expectedSize;
                break;
            case self::CROPCENTER:
            case self::CROPCENTRE:
                $size = $expectedSize / 2;
                break;
        }
        return $size;
    }
}



/**
 * Usage

To scale an image, in this case to half it's size (scaling is percentage based):

$image = new ImageResize('image.jpg');
$image->scale(50);
$image->save('image2.jpg')
To resize an image according to one dimension (keeping aspect ratio):

$image = new ImageResize('image.jpg');
$image->resizeToHeight(500);
$image->save('image2.jpg');

$image = new ImageResize('image.jpg');
$image->resizeToWidth(300);
$image->save('image2.jpg');
To resize an image to best fit a given set of dimensions (keeping aspet ratio):

$image = new ImageResize('image.jpg');
$image->resizeToBestFit(500, 300);
$image->save('image2.jpg');
To to crop an image:

$image = new ImageResize('image.jpg');
$image->crop(200, 200);
$image->save('image2.jpg');
This will scale the image to as close as it can to the passed dimensions, and then crop and center the rest.

In the case of the example above, an image of 400px × 600px will be resized down to 200px × 300px, and then 50px will be taken off the top and bottom, leaving you with 200px × 200px.

If you are happy to handle aspect ratios yourself, you can resize directly:

$image = new ImageResize('image.jpg');
$image->resize(800, 600);
$image->save('image2.jpg');
This will cause your image to skew if you do not use the same width/height ratio as the source image.

Loading and saving images from string

To load an image from a string:

$image = ImageResize::createFromString(base64_decode('R0lGODlhAQABAIAAAAQCBP///yH5BAEAAAEALAAAAAABAAEAAAICRAEAOw=='));
$image->scale(50);
$image->save('image.jpg');
You can also return the result as a string:

$image = ImageResize::createFromString(base64_decode('R0lGODlhAQABAIAAAAQCBP///yH5BAEAAAEALAAAAAABAAEAAAICRAEAOw=='));
$image->scale(50);
echo $image->getImageAsString();
Magic __toString() is also supported:

$image = ImageResize::createFromString(base64_decode('R0lGODlhAQABAIAAAAQCBP///yH5BAEAAAEALAAAAAABAAEAAAICRAEAOw=='));
$image->resize(10, 10);
echo (string)$image;
Displaying

As seen above, you can call $image->save('image.jpg');

To render the image directly into the browser, you can call $image->output();

Image Types

When saving to disk or outputting into the browser, the script assumes the same output type as input.

If you would like to save/output in a different image type, you need to pass a (supported) PHP IMAGETYPE_* constant:

IMAGETYPE_GIF
IMAGETYPE_JPEG
IMAGETYPE_PNG
This allows you to save in a different type to the source:

$image = new ImageResize('image.jpg');
$image->resize(800, 600);
$image->save('image.png', IMAGETYPE_PNG);
Quality

The properties $quality_jpg and $quality_png are available for you to configure:

$image = new ImageResize('image.jpg');
$image->quality_jpg = 100;
$image->resize(800, 600);
$image->save('image2.jpg');
By default they are set to 75 and 0 respectively. See the manual entries for imagejpeg() and imagepng() for more info.

You can also pass the quality directly to the save(), output() and getImageAsString() methods:

$image = new ImageResize('image.jpg');
$image->crop(200, 200);
$image->save('image2.jpg', null, 100);

$image = new ImageResize('image.jpg');
$image->resizeToWidth(300);
$image->output(IMAGETYPE_PNG, 4);

$image = new ImageResize('image.jpg');
$image->scale(50);
$result = $image->getImageAsString(IMAGETYPE_PNG, 4);
We're passing null for the image type in the example above to skip over it and provide the quality. In this case, the image type is assumed to be the same as the input.

Interlacing

By default, image interlacing is turned off. It can be enabled by setting $interlace to 1:

$image = new ImageResize('image.jpg');
$image->scale(50);
$image->interlace = 1;
$image->save('image2.jpg')
Chaining

When performing operations, the original image is retained, so that you can chain operations without excessive destruction.

This is useful for creating multiple sizes:

$image = new ImageResize('image.jpg');
$image
    ->scale(50)
    ->save('image2.jpg')

    ->resizeToWidth(300)
    ->save('image3.jpg')

    ->crop(100, 100)
    ->save('image4.jpg')
;
 */