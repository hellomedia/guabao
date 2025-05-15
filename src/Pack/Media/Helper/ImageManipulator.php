<?php

namespace App\Pack\Media\Helper;

use App\Entity\Media;

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
    final public const MAX_WIDTH = 2560; // 2K
    final public const MAX_HEIGHT = 2560; // 2K
    final public const MAX_PROFILE_WIDTH = 900;
    final public const MAX_PROFILE_HEIGHT = 1200;

    public function resizeImage(Media $image, int $maxWidth = self::MAX_WIDTH, int $maxHeight = self::MAX_HEIGHT)
    {
        try {
            $imagick = new \Imagick();

            $imagick->readImage($this->uploadsPath . '/' . $image->getPath());

            $this->autoRotateImage($imagick);

            $originalWidth = $imagick->getImageWidth();
            $originalHeight = $imagick->getImageHeight();

            // $image->setOriginalWidth($originalWidth);
            // $image->setOriginalHeight($originalHeight);

            $aspectRatio = $originalWidth / $originalHeight;

            // $image->setAspectRatio($aspectRatio);

            // resize on largest dimension
            if ($aspectRatio < 1) { // height > width
                $newHeight = min($originalHeight, $maxHeight);

                $imagick->thumbnailImage(0, $newHeight); // 0 maintains aspect ratio

                // $image->setHeight($newHeight);
                // $image->setWidth($newHeight * $aspectRatio);
            } else { // width >= height
                $newWidth = min($originalWidth, $maxWidth);

                $imagick->thumbnailImage($newWidth, 0); // 0 maintains aspect ratio

                // $image->setWidth($newWidth);
                // $image->setHeight($newWidth / $aspectRatio);
            }

            // default image quality setting is 85
            // https://www.the-art-of-web.com/system/imagemagick-quality/
            $imagick->setImageCompressionQuality(85);

            $imagick->writeImage();
        } catch (\Exception $e) {
            exit('Error when resizing uploaded image : '.$e->getMessage());
        }
    }

    public function autoRotateImage(\Imagick $imagick)
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
}
