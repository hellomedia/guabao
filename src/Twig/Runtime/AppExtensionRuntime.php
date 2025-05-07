<?php

namespace App\Twig\Runtime;

use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Tag\TripTag;
use App\Entity\Trip;
use App\Helper\Upload\UploadHelper;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class AppExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private UrlGeneratorInterface $router,
        private TranslatorInterface $translator,
        private UploadHelper $uploadHelper,
    ) {}

    public function translate(mixed $item, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        // DB translation
        if ($item instanceof LocalizedNameInterface) {
            return $item->getName($this->requestStack->getCurrentRequest()->getLocale());
        }

        // Enums etc
        if ($item instanceof TranslatableInterface) {
            return $item->trans($this->translator, $locale);
        }

        return $this->translator->trans($item, $parameters, $domain, $locale);
    }

    public function getUploadedAssetPath(string $path): string
    {
        return $this->uploadHelper->getPublicPath($path);
    }

    public function getAbsoluteUrl(mixed $item, array $parameters = []): string
    {
        return $this->getPath($item, $parameters, Router::ABSOLUTE_URL);
    }

    public function getPath(mixed $item, array $parameters = [], ?int $referenceType = Router::ABSOLUTE_PATH): string
    {
        if ($item instanceof Trip) {
            return $this->_getTripPath($item, $parameters, $referenceType);
        }

        return $this->router->generate($item, $parameters, $referenceType);
    }

    private function _getTripPath(Trip $trip, array $parameters, int $referenceType): string
    {
        $locale = $parameters['_locale'] ?? $this->requestStack->getCurrentRequest()->getLocale();

        return $this->router->generate('trip_show', [
            'slug' => $trip->getSlug($locale),
            '_locale' => $locale,
        ], $referenceType);
    }

    // private function _getListingPath(Listing $listing, array $parameters, int $referenceType): string
    // {
    //     $locale = $parameters['_locale'] ?? $this->requestStack->getCurrentRequest()->getLocale();

    //     return $this->router->generate('listing_show', [
    //         'id' => $listing->getId(),
    //         'slug' => $listing->getSlug($locale),
    //         'category_slug' => $listing->getCategory()->getSlug($locale),
    //         'subcategory_slug' => $listing->getSubcategory()->getSlug($locale),
    //         '_locale' => $locale,
    //     ], $referenceType);
    // }

    public function isCurrentPage(string $route, array $parameters = []): bool
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return false;
        }

        if ($route !== $request->attributes->get('_route')) {
            return false;
        }

        foreach ($parameters as $key => $value) {
            if ($request->attributes->get($key) !== $value) {
                return false;
            }
        }

        return true;
    }
}
