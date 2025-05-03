<?php

namespace App\Helper\Upload;

use App\Entity\Listing;
use App\Entity\Image;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * https://symfonycasts.com/screencast/symfony-uploads
 */
class UploadHelper
{
    final public const MAX_IMAGE_SIZE = '30M';

    // directories inside uploads dir
    final public const LISTING_IMAGE_DIR = 'listing_images';

    public function __construct(
        private RequestStackContext $requestStackContext,
        private string $uploadsPath,
        private string $uploadedAssetsBaseUrl,
        private ImageManipulator $imageManipulator,
        private ImageConverter $imageConverter,
        private TranslatorInterface $translator,
        private SluggerInterface $slugger,
    ) {}

    /**
     * $file is an UploadedFile -- which extends File -- except for data fixtures
     * when we can not create a fake UploadedFile and create a File instead
     */
    public function uploadListingImage(File $file, Listing $listing): Image
    {
        $image = $this->_uploadAndSave($file, $listing);

        $this->_optimizeImage($image);

        return $image;
    }

    /**
     * Complete Upload process:
     *  - Move uploaded file from temporary location to uploads directory
     *  - Rename file with unique name to avoid conflicts
     *  - save filename, token and path in new image entity
     */
    private function _uploadAndSave(File $file, Listing $listing): Image
    {
        // https://symfonycasts.com/screencast/symfony-uploads/file-naming
        $token = uniqid();

        if ($file instanceof UploadedFile) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        } else {
            // data fixtures
            $originalFilename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        }

        $newFilename = $this->slugger->slug(mb_substr($originalFilename, 0, 100)) . '-' .
            $this->slugger->slug(\mb_strtolower($listing->getSubcategory()->getSingularFr())) . '-' .
            $token . '.' .
            $file->guessExtension();

        $image = new Image();

        $image->setToken($token);
        $image->setOriginalFilename($originalFilename);
        $image->setFilename($newFilename);
        $image->setPath(self::LISTING_IMAGE_DIR);
        $image->setListing($listing);

        $destination = $this->uploadsPath . '/' . self::LISTING_IMAGE_DIR . '/' . $image->getSubDirs();
    
        $file->move($destination, $newFilename);

        return $image;
    }

    private function _optimizeImage(Image $image): void
    {
        $this->imageManipulator->resizeImage($image);

        $this->imageConverter->convertToAvif($image);
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
    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext->getBasePath() .
            $this->uploadedAssetsBaseUrl . '/' .
            $path;
    }
}
