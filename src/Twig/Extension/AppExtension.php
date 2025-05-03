<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\AppExtensionRuntime;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private RequestStack $requestStack,
    ) {

    }

    /**
     * NB: Simple globals can be set in config/packages/twig.yaml
     */
    public function getGlobals(): array
    {
        // set in ControllerSubscriber
        $theme = $this->requestStack->getCurrentRequest()?->attributes->get('theme', 'light');

        return [
            'theme' => $theme,
        ];
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('trans', [AppExtensionRuntime::class, 'translate'], ['is_safe' => ['html']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('absolute_url', [AppExtensionRuntime::class, 'getAbsoluteUrl']),
            new TwigFunction('uploaded_asset', [AppExtensionRuntime::class, 'getUploadedAssetPath']),
            new TwigFunction('is_current_page', [AppExtensionRuntime::class, 'isCurrentPage']),
        ];
    }
}
