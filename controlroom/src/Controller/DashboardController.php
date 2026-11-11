<?php

namespace Controlroom\Controller;

use App\Entity\Food;
use App\Entity\Interface\EntityInterface;
use App\Entity\Media;
use App\Entity\Place;
use App\Entity\Trip;
use App\Repository\Stats\AnonymousVisitorRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\Font;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\GrayScale;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\ThemeColor;
use EasyCorp\Bundle\EasyAdminBundle\Config\Theme;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

// For localized easyadmin, use routePath: '/{_locale}/'
// setting locale prefix in easyadmin.yaml does not seem to work
// detail route constrained to numeric ids: EA generates its catch-all /<crud>/{entityId} before our
// #[AdminRoute] pages, which would otherwise swallow /cuisine/bulk-edit, /media/bulk-add, etc.
#[AdminDashboard(routePath: '/', routeName: 'controlroom', routes: ['detail' => ['routePath' => '/{entityId<\\d+>}']])]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private AnonymousVisitorRepository $anonymousVisitorRepository,
    )
    {
    }

    public function index(): Response
    {
        $notableVisitorsOrderedByLastSeen = $this->anonymousVisitorRepository->findNotableVisitorsOrderedByLastSeen();
        $notableVisitorsOrderedByPageCount = $this->anonymousVisitorRepository->findNotableVisitorsOrderedByPageCount();

        return $this->render('@admin/dashboard.html.twig', [
            'notable_visitors_by_last_seen' => $notableVisitorsOrderedByLastSeen,
            'notable_visitors_by_page_count' => $notableVisitorsOrderedByPageCount,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Guabao')
            // EasyAdmin 5.9+ design tokens: one place to restyle the whole backend
            // https://symfony.com/bundles/EasyAdminBundle/current/design.html
            ->setTheme(Theme::new()
                ->primaryColor(ThemeColor::Teal)
                ->grays(GrayScale::SLATE)
                ->radius('lg')
                ->font(Font::IbmPlexSans)
            )
        ;
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setDateTimeFormat('d/MM/yy HH:mm') // https://unicode-org.github.io/icu/userguide/format_parse/datetime/
            ->setPaginatorPageSize('100')
            ->setDefaultSort(['id' => 'DESC'])
            ->setAutofocusSearch()
            ->showEntityActionsInlined()
            ->setFormThemes(['@admin/form/form_theme.html.twig'])
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

        yield MenuItem::subMenu('Status', 'fa fa-dashboard')
            ->setSubItems([
                MenuItem::linkToRoute('Opcache', 'fa fa-dashboard', 'controlroom_status_opcache'),
                MenuItem::linkToRoute('Realpath cache', 'fa fa-dashboard', 'controlroom_status_realpath_cache'),
                MenuItem::linkToUrl('Php-fpm', 'fa fa-dashboard', '/status/php-fpm'),
                MenuItem::linkToRoute('Php info', 'fa fa-info', 'controlroom_status_phpinfo'),
            ]);

        yield MenuItem::section('Site');
        yield MenuItem::linkTo(SiteHighlightCrudController::class, 'Highlights', 'fa fa-star');

        yield MenuItem::section('Trips');
        yield MenuItem::linkTo(TripCrudController::class, 'Trips', 'fa fa-globe');
        yield MenuItem::linkTo(StoryCrudController::class, 'Stories', 'fa fa-comment-dots');
        yield MenuItem::linkTo(TripTagCrudController::class, 'Trip Tags', 'fa fa-tag');

        yield MenuItem::section('Food');
        yield MenuItem::linkTo(FoodCrudController::class, 'Food', 'fa fa-pizza-slice');
        yield MenuItem::linkTo(MealCrudController::class, 'Meals', 'fa fa-utensils');
        yield MenuItem::linkTo(IngredientCrudController::class, 'Ingredients', 'fa fa-carrot');
        yield MenuItem::linkTo(FoodTagCrudController::class, 'Food Tags', 'fa fa-tag');
        yield MenuItem::linkTo(CuisineCrudController::class, 'Cuisines', 'fa fa-utensils');

        yield MenuItem::section('Medias');
        yield MenuItem::linkTo(ImageCrudController::class, 'Images', 'fa fa-photo');
        yield MenuItem::linkTo(VideoCrudController::class, 'Videos', 'fa fa-video');
        yield MenuItem::linkTo(MediaTagCrudController::class, 'Media Tags', 'fa fa-tag');
        yield MenuItem::linkToUrl('Unlinked images', 'fa fa-photo', $this->urlGenerator->generate('controlroom_media_bulk_edit_unlinked'));

        yield MenuItem::section('Places');
        yield MenuItem::linkTo(PlaceCrudController::class, 'Places', 'fa fa-map-marker');
        yield MenuItem::linkTo(ChainCrudController::class, 'Chains', 'fa fa-store');
        yield MenuItem::linkTo(PlaceTagCrudController::class, 'Place Tags', 'fa fa-tag');
        yield MenuItem::linkTo(CountryCrudController::class, 'Countries', 'fa fa-flag');

        yield MenuItem::section('Users');
        yield MenuItem::linkTo(UserCrudController::class, 'Users', 'fa fa-user');

        yield MenuItem::section('Stats');
        yield MenuItem::linkTo(AnonymousVisitorCrudController::class, 'Visitors', 'fa fa-user');
        yield MenuItem::linkTo(AnonymousVisitCrudController::class, 'Visits', 'fa fa-user');
        yield MenuItem::linkTo(AnonymousPageViewCrudController::class, 'Page views', 'fa fa-user');
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
                return $action->setLabel('Sauver → Edit')->setIcon(null);
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
