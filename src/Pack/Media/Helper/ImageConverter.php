<?php

namespace App\Pack\Media\Helper;

use App\Entity\Media;
use Imagick;

class ImageConverter
{
    public function __construct(
        private string $uploadsPath,
    )
    {
    }

    public function convertToAvif(Media $image): void
    {
        $imagick = new Imagick();

        $originalPath = $this->uploadsPath . '/' . $image->getPath();

        $imagick->readImage($originalPath);

        $format = $imagick->getImageFormat(); // Imagick::queryFormats() to see formats

        if ($format !== 'AVIF') {

            $newFilename = $this->_replaceExtensionWithAvif($image->getFilename());

            $image->setFilename($newFilename);
            $image->setPath(UploadHelper::IMAGE_DIR);

            $imagick->setImageFormat('AVIF');
            // For AFIF, avoid $imagick->setCompressionQuality(), it gives gives very bad results
            // $imagick->setImageCompressionQuality(80) should but does not do anything, it seems harmless
            // test : 600K optimized jpg ---> 200k avif
            $imagick->setImageCompressionQuality(80);
            $imagick->setImageDepth(8);

            $newPath = $this->uploadsPath . '/' . $image->getPath();

            $imagick->writeImage($newPath);

            $imagick->clear();

            // remove old image file
            if (file_exists($originalPath)) {
                unlink($originalPath);
            }
        }
    }

    private function _replaceExtensionWithAvif(string $filename): string
    {
        $pathInfo = pathinfo($filename);

        return $pathInfo['filename'] . '.avif';
    }
}
