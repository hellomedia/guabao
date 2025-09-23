<?php

namespace Pack\Media\Twig\Extension;

use Pack\Media\Twig\Runtime\UploadedAssetExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UploadedAssetExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploaded_asset', [UploadedAssetExtensionRuntime::class, 'getUploadedAssetPath']),
        ];
    }
}
