<?php 

namespace App\Helper;

use App\Entity\Ingredient;
use App\Entity\Trip;
use Symfony\Component\HttpFoundation\RequestStack;

class BreadcrumbsHelper
{
    public function __construct(
        private RequestStack $requestStack,
    )
    {
    }

    private array $breadcrumbs = [];

    public function addBreadcrumb(Trip|Ingredient|string $item, ?string $route = null, ?array $routeParams = [], ?bool $isLarge = null, ?bool $isAdmin = null): void
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        if ($item instanceof Trip) {
            $route = 'trip_show';
            $routeParams = [
                'slug' => $item->getSlug($locale)
            ];
            $item = $item->getName($locale) . ' ' . $item->getPeriod();
        }
    
        if ($item instanceof Ingredient) {
            $route = 'food_by_ingredient';
            $routeParams = [
                'slugEn' => $item->getSlugEn(),
            ];
            $item = $item->getName($locale);
        }
    
        $this->breadcrumbs[] = [
            'item' => $item,
            'route' => $route,
            'routeParams' => $routeParams,
            'large' => $isLarge,
        ];
    }

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }
}
