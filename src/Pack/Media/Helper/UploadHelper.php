<?php

namespace App\Pack\Media\Helper;

use App\Pack\Media\Entity\Interface\UploadedAssetEntityInterface;
use App\Pack\Media\Helper\ImageManipulator;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * https://symfonycasts.com/screencast/symfony-uploads
 * 
 * 
 * config/parameters.yaml:
 * uploads_dir: 'uploads'
 * uploads_base_url: '/%uploads_dir%'
 */
class UploadHelper
{
    final public const MAX_IMAGE_SIZE = '30M';

    // directories inside uploads dir
    final public const IMAGE_DIR = 'image';

    public function __construct(
        private RequestStackContext $requestStackContext,
        private string $uploadsPath,
        private string $uploadedAssetsBaseUrl,
        private ImageManipulator $imageManipulator,
        private TranslatorInterface $translator,
        private SluggerInterface $slugger,
    ) {}

    public function uploadImage(UploadedAssetEntityInterface $image, UploadedFile $file, ?bool $resize = false, ?bool $convert = true)
    {
        $this->_uploadAndSavePath($image, $file);

        $this->_optimizeImage($image, resize: $resize, convert: $convert);
    }

    public function createUploadedFileForFixtures(string $originalPath): UploadedFile
    {
        // Copy to a temporary file (unique filename each time)
        // so that original file is not moved by upload process and still avaialable for next time
        $tempPath = sys_get_temp_dir() . '/' . uniqid('upload_', true) . '.jpg';
        copy($originalPath, $tempPath);

        $uploadedFile = new UploadedFile(
            $tempPath,
            basename($originalPath),
            null,
            null,
            true // true = test mode, necessary for fixtures, skips file upload checks
        );

        return $uploadedFile;
    }

    /**
     * Complete Upload process:
     *  - Move uploaded file from temporary location to uploads directory
     *  - Rename file with unique name to avoid conflicts
     *  - save filename, token and path in new image entity
     */
    private function _uploadAndSavePath(UploadedAssetEntityInterface $image, UploadedFile $file)
    {
        // https://symfonycasts.com/screencast/symfony-uploads/file-naming
        $token = uniqid();

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        /* Naming strategy safety */
        // Extracting the base filename: pathinfo(..., PATHINFO_FILENAME) removes the extension (good)
        // Limiting length: mb_substr(..., 0, 100) avoids overly long filenames.
        // Slugging: $this->slugger->slug(...) sanitizes the filename to safe characters (usually alphanumeric + - or _).
        // Appending a unique token: $token helps avoid collisions.
        // Guessing the extension: guessExtension() is often reliable
    
        $newFilename = $this->slugger->slug(mb_substr($originalFilename, 0, 100)) . '-' .
            $token . '.' .
            $file->guessExtension();

        $image->setToken($token);
        $image->setOriginalFilename($originalFilename);
        $image->setFilename($newFilename);
        $image->setPath(imageUploadsDir: self::IMAGE_DIR);

        $destination = $this->uploadsPath . '/' . self::IMAGE_DIR . '/' . $image->getSubDirs();
    
        $file->move($destination, $newFilename);
    }

    private function _optimizeImage(UploadedAssetEntityInterface $image, ?bool $resize = false, ?bool $convert = true): void
    {
        // more efficient to do all image manipulation in 1 go
        // for CPU and for image quality
        $this->imageManipulator->handleImageSizing($image, resize: $resize, convert: $convert);
    }

    /**
     * Returns public path for uploaded assets, for use in twig uploaded_asset function
     *
     * https://symfonycasts.com/screencast/symfony-uploads/assets-context
     * 
     * https://symfonycasts.com/screencast/symfony-uploads/absolute-asset-paths
     * 
     * NB: Only works when requestStack is set (not in command or queue).
     * In command or queue, use siteProvider::getAbsoluteUrl() as in HK
     * or refer to the solution in the symfony cast episode
     */
    public function getPublicPath(UploadedAssetEntityInterface $uploadedAsset): string
    {
        return $this->requestStackContext->getBasePath() .
            $this->uploadedAssetsBaseUrl . '/' .
            $uploadedAsset->getPath();
    }
}
