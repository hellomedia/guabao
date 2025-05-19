<?php

namespace App\Pack\Media\Helper;

use App\Entity\Media;
use Imagick;

class ImageManipulator
{

    public function __construct(
        private string $uploadsPath,
    )
    {
        
    }

    // // What max size should we keep ?
    // // full HD is 1920x1080 , or 1080p
    // // 2K is 2560x1440
    // // 2400x1600 is our setting for photo shootings
    // // This allows us to keep original quality from shootings
    final public const MAX_WIDTH = 2560; // 2K;
    final public const MAX_WIDTH_PANO = 5120; // 4K
    final public const MAX_WIDTH_360 = 9000;
    final public const MAX_HEIGHT = 2560; // 2K
    final public const MAX_PROFILE_WIDTH = 900;
    final public const MAX_PROFILE_HEIGHT = 1200;

    /**
     * More efficient in terms of image quality to handle all image manipulation in 1 go:
     * - resizing
     * - conversion
     * 
     * instead of resizing (or saving withtout resizing but still degrading the image quality), then converting
     */
    public function optimize(Media $image, ?bool $resize = false, ?bool $convert = true, int $maxWidth = self::MAX_WIDTH, int $maxHeight = self::MAX_HEIGHT)
    {
        try {
            $imagick = new \Imagick();

            $originalPath = $this->uploadsPath . '/' . $image->getPath();

            $imagick->readImage($originalPath);

            $this->_autoRotateImage($imagick);

            $image->setOriginalWidth($imagick->getImageWidth());
            $image->setOriginalHeight($imagick->getImageHeight());

            if ($resize) {
                $this->_resize($imagick, $image, $maxWidth, $maxHeight);
            }

            $image->setWidth($imagick->getImageWidth());
            $image->setHeight($imagick->getImageHeight());

            if ($convert) {
                $this->_convertToAvif($imagick, $image);
            }

            // For AVIF, avoid $imagick->setCompressionQuality(), gives bad results
            // $imagick->setImageCompressionQuality(80) should but does not do anything, it seems harmless
            // test : 600K optimized jpg ---> 200k avif
            // https://www.the-art-of-web.com/system/imagemagick-quality/
            $imagick->setImageCompressionQuality(85);

            // path changed if avif conversion
            // otherwise, newPath = originalPath, it's OK
            $newPath = $this->uploadsPath . '/' . $image->getPath();

            $imagick->writeImage($newPath);

            $imagick->clear();

            // remove old image file if there was a format conversion
            if (file_exists($originalPath) && $newPath != $originalPath) {
                unlink($originalPath);
            }

        } catch (\Exception $e) {
            exit('Error when resizing uploaded image : '.$e->getMessage());
        }
    }

    private function _autoRotateImage(\Imagick $imagick)
    {
        $orientation = $imagick->getImageOrientation();

        switch ($orientation) {
            case \Imagick::ORIENTATION_UNDEFINED:
                break;

            case \Imagick::ORIENTATION_TOPRIGHT:
                $imagick->flopimage();
                break;

            case \Imagick::ORIENTATION_BOTTOMRIGHT:
                $imagick->rotateimage('#000', 180); // rotate 180 degrees
                break;

            case \Imagick::ORIENTATION_BOTTOMLEFT:
                $imagick->flipimage();
                break;

            case \Imagick::ORIENTATION_LEFTTOP:
                $imagick->rotateimage('#000', 90);
                $imagick->flopimage();
                break;

            case \Imagick::ORIENTATION_RIGHTTOP:
                $imagick->rotateimage('#000', 90); // rotate 90 degrees CW
                break;

            case \Imagick::ORIENTATION_RIGHTBOTTOM:
                $imagick->rotateimage('#000', -90);
                $imagick->flopimage();
                break;

            case \Imagick::ORIENTATION_LEFTBOTTOM:
                $imagick->rotateimage('#000', -90); // rotate 90 degrees CCW
                break;
        }

        // Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image!
        $imagick->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);
    }

    private function _resize(Imagick $imagick, Media $image, int $maxWidth = self::MAX_WIDTH, int $maxHeight = self::MAX_HEIGHT): void
    {
        $originalWidth = $imagick->getImageWidth();
        $originalHeight = $imagick->getImageHeight();
        $aspectRatio = $originalWidth / $originalHeight;

        // resize on largest dimension
        if ($aspectRatio < 1) { // height > width
            $newHeight = min($originalHeight, $maxHeight);

            $imagick->thumbnailImage(0, $newHeight); // 0 maintains aspect ratio

            $image->setHeight($newHeight);
            $image->setWidth($newHeight * $aspectRatio);
        } else { // width >= height
            $newWidth = min($originalWidth, $maxWidth);

            $imagick->thumbnailImage($newWidth, 0); // 0 maintains aspect ratio

            $image->setWidth($newWidth);
            $image->setHeight($newWidth / $aspectRatio);
        }
    }

    private function _convertToAvif(Imagick $imagick, Media $image): void
    {
        if ($imagick->getImageFormat() !== 'AVIF') {

            $newFilename = $this->_replaceExtensionWithAvif($image->getFilename());

            $image->setFilename($newFilename);

            // filename must be set first
            $image->setPath(imageUploadsDir: UploadHelper::IMAGE_DIR); 

            $imagick->setImageFormat('AVIF');
        }
    }

    private function _replaceExtensionWithAvif(string $filename): string
    {
        $pathInfo = pathinfo($filename);

        return $pathInfo['filename'] . '.avif';
    }
}
