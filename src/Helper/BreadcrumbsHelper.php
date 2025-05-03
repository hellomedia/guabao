<?php 

namespace App\Helper;

use App\Entity\Category;
use App\Entity\Listing;
use App\Entity\Subcategory;
use Symfony\Component\HttpFoundation\RequestStack;

class BreadcrumbsHelper
{
    public function __construct(
        private RequestStack $requestStack,
    )
    {
    }

    private array $breadcrumbs = [];

    public function addBreadcrumb(Category|Subcategory|Listing|string $item, ?string $route = null, ?array $routeParams = [], ?bool $isAdmin = null): void
    {
        if ($item instanceof Category) {
            $route = 'category_index';
            $routeParams = [
                'slug' => $item->getSlug($this->requestStack->getCurrentRequest()->getLocale())
            ];
        }

        if ($item instanceof Subcategory) {
            $route = 'subcategory_index';
            $routeParams = [
                'category_slug' => $item->getCategory()->getSlug($this->requestStack->getCurrentRequest()->getLocale()),
                'subcategory_slug' => $item->getSlug($this->requestStack->getCurrentRequest()->getLocale()),
            ];
        }

        if ($item instanceof Listing) {
            if ($isAdmin) {
                $route = 'admin_listing_show';
                $routeParams = ['id' => $item->getId()];
            } else {
                $route = 'listing_show';
                $routeParams = [
                    'id' => $item->getId(),
                    'category_slug' => $item->getCategory()->getSlug($this->requestStack->getCurrentRequest()->getLocale()),
                    'subcategory_slug' => $item->getSubcategory()->getSlug($this->requestStack->getCurrentRequest()->getLocale()),
                ];
            }
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
