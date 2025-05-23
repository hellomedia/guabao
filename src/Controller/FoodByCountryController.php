<?php

namespace App\Controller;

use App\Entity\Country;
use App\Entity\Food;
use App\Repository\CountryRepository;
use App\Repository\MediaRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FoodByCountryController extends BaseController
{
    public function preExecute()
    {
        $this->addBreadcrumb('homepage', 'homepage');
    }

    #[Route('/food/country', name: 'food_by_country_index')]
    public function index(CountryRepository $countryRepository): Response
    {
        $countries = $countryRepository->findAll();

        $this->addBreadcrumb('food.by_country');

        return $this->render('food/country/index.html.twig', [
            'countries' => $countries
        ]);
    }

    #[Route('/food/country/{slugEn:country}', name: 'food_by_country_country')]
    public function country(Country $country, MediaRepository $mediaRepository, Request $request): Response
    {
        $medias = $mediaRepository->findFoodMediasByCountry($country);

        $this->addBreadcrumb('food.by_country', 'food_by_country_index');
        $this->addBreadcrumb($country->getName($request->getLocale()));

        return $this->render('food/country/country.html.twig', [
            'medias' => $medias,
            'country' => $country,
        ]);
    }

    #[Route('/food/country/{slugCountry}/{slugEn:food}', name: 'food_by_country_food')]
    public function food(
        #[MapEntity(expr: 'repository.findOneBySlugEn(slugCountry)')] Country $country,
        Food $food,
        Request $request
    ): Response {
        $this->addBreadcrumb('food.by_country', 'food_by_country_index');
        $this->addBreadcrumb($country->getName(
            $request->getLocale()), 'food_by_country_country',
            ['slugEn' => $country->getSlugEn()],
            isLarge: true
        );
        $this->addBreadcrumb($food->getName($request->getLocale()));

        return $this->render('food/food.html.twig', [
            'food' => $food,
            'meals' => $food->getMeals(),
        ]);
    }
}
