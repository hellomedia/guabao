<?php 

namespace App\Helper;

use App\Entity\FoodItem;
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

    public function addBreadcrumb(Trip|FoodItem|string $item, ?string $route = null, ?array $routeParams = [], ?bool $isAdmin = null): void
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        if ($item instanceof Trip) {
            $route = 'trip_show';
            $routeParams = [
                'slug' => $item->getSlug($locale)
            ];
            $item = $item->getName($locale) . ' ' . $item->getPeriod();
        }
    
        $this->breadcrumbs[] = [
            'item' => $item,
            'route' => $route,
            'routeParams' => $routeParams,
        ];
    }

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }
}
