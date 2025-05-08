<?php

namespace Controlroom\Controller;

use App\Entity\Country;
use App\Entity\Food;
use App\Entity\Ingredient;
use App\Entity\Interface\EntityInterface;
use App\Entity\Meal;
use App\Entity\Picture;
use App\Entity\Place;
use App\Entity\Tag\FoodTag;
use App\Entity\Tag\PictureTag;
use App\Entity\Tag\PlaceTag;
use App\Entity\Tag\Tag;
use App\Entity\Tag\TripTag;
use App\Entity\Trip;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AdminDashboard(routePath: '/', routeName: 'controlroom')]
class DashboardController extends AbstractDashboardController
{
    // route attribute here is deprecated and should be removed in next version
    // but at the moment, removing it cause error: 'missing controlroom_dashboard' route
    #[Route('/', name: 'controlroom_dashboard')]
    public function index(): Response
    {
        return $this->render('@controlroom/dashboard.html.twig', [
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Guabao')
        ;
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setDateTimeFormat('d/MM/yy HH:mm') // https://unicode-org.github.io/icu/userguide/format_parse/datetime/
            ->setPaginatorPageSize('30')
            ->setDefaultSort(['id' => 'DESC'])
            ->setAutofocusSearch()
            ->showEntityActionsInlined()
            ->setFormThemes(['@controlroom/form_theme.html.twig',  '@EasyAdmin/crud/form_theme.html.twig'])
            ->setPageTitle(Crud::PAGE_DETAIL, static function (EntityInterface $entity) {
                return $entity;
            })
            ->setPageTitle(Crud::PAGE_EDIT, static function (EntityInterface $entity) {
                return 'Modifier ' . $entity;
            })
        ;
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            // imports the given entrypoint defined in the importmap.php file of AssetMapper
            // it's equivalent to adding this inside the <head> element:
            // {{ importmap('controlroom') }}
            ->addAssetMapperEntry('controlroom')
            // you can also import multiple entries
            // it's equivalent to calling {{ importmap(['app', 'admin']) }}
            //->addAssetMapperEntry('app', 'admin')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-dashboard');

        yield MenuItem::section('Food');
        yield MenuItem::linkToCrud('Food', 'fa fa-lemon', Food::class);
        yield MenuItem::linkToCrud('Meal', 'fa fa-meal', Meal::class);
        yield MenuItem::linkToCrud('Ingredient', 'fa fa-lemon', Ingredient::class);
        yield MenuItem::linkToCrud('Food Tag', 'fa fa-tag', FoodTag::class);

        yield MenuItem::section('Trips');
        yield MenuItem::linkToCrud('Trip', 'fa fa-globe', Trip::class);
        yield MenuItem::linkToCrud('Trip Tag', 'fa fa-tag', TripTag::class);

        yield MenuItem::section('Pictures');
        yield MenuItem::linkToCrud('Picture', 'fa fa-photo', Picture::class);
        yield MenuItem::linkToCrud('Picture Tag', 'fa fa-tag', PictureTag::class);

        yield MenuItem::section('Places');
        yield MenuItem::linkToCrud('Place', 'fa fa-map-marker', Place::class);
        yield MenuItem::linkToCrud('Place Tag', 'fa fa-tag', PlaceTag::class);
        yield MenuItem::linkToCrud('Country', 'fa fa-map-marker', Country::class);

        yield MenuItem::section('Users');
        yield MenuItem::linkToCrud('User', 'fa fa-user', User::class);
    }

    public function configureActions(): Actions
    {
        return Actions::new()
            ->disable(Action::BATCH_DELETE)

            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DELETE)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE])

            ->add(Crud::PAGE_DETAIL, Action::DELETE)
            ->add(Crud::PAGE_DETAIL, Action::INDEX)
            ->add(Crud::PAGE_DETAIL, Action::EDIT)

            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN)
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) {
                return $action->setLabel('Sauver → List');
            })
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, function (Action $action) {
                return $action->setLabel('Sauver → Edit')->setIcon(false);
            })
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)

            ->add(Crud::PAGE_NEW, Action::SAVE_AND_RETURN)
            ->add(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_NEW, Action::INDEX)
        ;
    }

    public function configureFilters(): Filters
    {
        return Filters::new()
            ->add('id')
        ;
    }
}
