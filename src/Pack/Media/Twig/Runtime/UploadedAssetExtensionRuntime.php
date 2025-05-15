<?php

namespace App\Pack\Media\Twig\Runtime;

use App\Pack\Media\Entity\Interface\UploadedAssetEntityInterface;
use App\Pack\Media\Helper\UploadHelper;
use Twig\Extension\RuntimeExtensionInterface;

class UploadedAssetExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private UploadHelper $uploadHelper,
    ) {}

    public function getUploadedAssetPath(UploadedAssetEntityInterface $uploadedAsset): string
    {
        return $this->uploadHelper->getPublicPath($uploadedAsset);
    }
}
