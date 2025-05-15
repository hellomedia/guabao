<?php 

namespace App\Pack\Media\Helper;

use Symfony\Component\HttpFoundation\File\File;

class ExifExtractor
{
    public function extractExifData(File $file): array|false
    {
        if (!$file instanceof \SplFileInfo || !file_exists($file->getPathname())) {
            return false;
        }

        return @exif_read_data($file->getPathname());
    }
}