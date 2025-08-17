<?php

namespace Controlroom\Controller;

use App\Controller\BaseController;
use App\Entity\Media;
use App\Entity\Story;
use App\Entity\Trip;
use Controlroom\Form\Type\MediaQuickEditType;
use Controlroom\Form\Type\StoryQuickEditType;
use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaBulkEditController extends BaseController
{
    public function __construct(
        private EntityManager $entityManager,
    )
    {
    }

    // EA defaults option add the ea variable in twig, needed to extend easyadmin templates
    // see https://github.com/EasyCorp/EasyAdminBundle/pull/6765
    // NB: this was not necessary with the old ea routing strategy
    #[Route('/trip/{id:trip}/media/bulk-edit/{page<\d+>?1}', name: 'admin_media_bulk_edit_by_trip', methods: ['GET'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function bulkEditByTrip(Trip $trip, int $page, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        $queryBuilder = $entityManager->getRepository(Media::class)
            ->getFindByTripQueryBuilder($trip, adminList: true);

        $pager = Pagerfanta::createForCurrentPageWithMaxPerPage(
            adapter: new QueryAdapter($queryBuilder),
            currentPage: $page,
            maxPerPage: 50,
        );

        $forms = [];
        foreach ($pager as $media) {
            $forms[$media->getId()] = $formFactory->createNamed(
                // form names must match between bulk edit forms and ajax edit form
                name: 'media_quick_edit_form_' . $media->getId(),
                type: MediaQuickEditType::class,
                data: $media,
                options: ['trip' => $trip]
            )->createView();
        }

        return $this->render('@controlroom/media/bulk_edit_by_trip.html.twig', [
            'forms' => $forms,
            'pager' => $pager,
            'trip' => $trip,
        ]);
    }

    // EA defaults option add the ea variable in twig, needed to extend easyadmin templates
    // see https://github.com/EasyCorp/EasyAdminBundle/pull/6765
    // NB: this was not necessary with the old ea routing strategy
    #[Route('/story/{id:story}/media/bulk-edit', name: 'admin_media_bulk_edit_by_story', methods: ['GET'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function bulkEditByStory(Story $story, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        $medias = $entityManager->getRepository(Media::class)->findByStory($story);

        $storyForm = $formFactory->createNamed(
            // form names must match with story quick edit form
            name: 'story_quick_edit_form_' . $story->getId(),
            type: StoryQuickEditType::class,
            data: $story,
        );

        $forms = [];
        foreach ($medias as $media) {
            $forms[$media->getId()] = $formFactory->createNamed(
                // form names must match with media quick edit form
                name: 'media_quick_edit_form_' . $media->getId(),
                type: MediaQuickEditType::class,
                data: $media,
                options: ['trip' => $story->getTrip()]
            )->createView();
        }

        return $this->render('@controlroom/media/bulk_edit_by_story.html.twig', [
            'story_form' => $storyForm,
            'forms' => $forms,
            'medias' => $medias,
            'story' => $story,
        ]);
    }
}