<?php

namespace Controlroom\Controller;

use App\Controller\BaseController;
use App\Entity\Food;
use App\Entity\Media;
use App\Entity\Story;
use App\Entity\Trip;
use Controlroom\Form\Type\FoodQuickEditType;
use Controlroom\Form\Type\MediaBulkEditTagsType;
use Controlroom\Form\Type\MediaQuickEditType;
use Controlroom\Form\Type\StoryQuickEditType;
use Controlroom\Form\Type\TripQuickEditType;
use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
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

        // FORM 1 (array of forms): Quick edit individual trip medias
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

        // FORM 2: Quick Edit Trip 
        $tripForm = $formFactory->createNamed(
            // form names must match with story quick edit form
            name: 'trip_quick_edit_form_' . $trip->getId(),
            type: TripQuickEditType::class,
            data: $trip,
        );

        return $this->render('@admin/media/bulk_edit_by_trip.html.twig', [
            'trip_form' => $tripForm,
            'forms' => $forms,
            'pager' => $pager,
            'trip' => $trip,
        ]);
    }

    // EA defaults option add the ea variable in twig, needed to extend easyadmin templates
    // see https://github.com/EasyCorp/EasyAdminBundle/pull/6765
    // NB: this was not necessary with the old ea routing strategy
    #[Route('/story/{id:story}/media/bulk-edit', name: 'admin_media_bulk_edit_by_story', methods: ['GET', 'POST'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function bulkEditByStory(Story $story, Request $request, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        $bulkAddTagsForm = $formFactory->createNamed(
            // form names must match with story quick edit form
            name: 'media_bulk_add_tags_form_' . $story->getId(),
            type: MediaBulkEditTagsType::class,
            options: ['story' => $story],
        );

        $bulkRemoveTagsForm = $formFactory->createNamed(
            // form names must match with story quick edit form
            name: 'media_bulk_remove_tags_form_' . $story->getId(),
            type: MediaBulkEditTagsType::class,
            options: ['story' => $story],
        );

        $storyForm = $formFactory->createNamed(
            // form names must match with story quick edit form
            name: 'story_quick_edit_form_' . $story->getId(),
            type: StoryQuickEditType::class,
            data: $story,
        );

        $medias = $entityManager->getRepository(Media::class)->findByStory($story);

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

        return $this->render('@admin/media/bulk_edit_by_story.html.twig', [
            'story_form' => $storyForm,
            'bulk_add_tags_form' => $bulkAddTagsForm,
            'bulk_remove_tags_form' => $bulkRemoveTagsForm,
            'forms' => $forms,
            'medias' => $medias,
            'story' => $story,
        ]);
    }

    // EA defaults option add the ea variable in twig, needed to extend easyadmin templates
    // see https://github.com/EasyCorp/EasyAdminBundle/pull/6765
    // NB: this was not necessary with the old ea routing strategy
    #[Route('/story/{id:story}/media/bulk-add-tags', name: 'admin_media_bulk_add_tags_by_story', methods: ['GET', 'POST'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function bulkAddTagsByStory(Story $story, Request $request, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        $form = $formFactory->createNamed(
            name: 'media_bulk_add_tags_form_' . $story->getId(),
            type: MediaBulkEditTagsType::class,
            options: ['story' => $story],
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->_processBulkAddTagsForm($form, $story);
           
            $entityManager->flush();

            $this->addFlash('success', 'Tag added successfully');
        }

        return $this->redirectToRoute('admin_media_bulk_edit_by_story', [
            'id' => $story->getId(),
        ]);
    }

    // EA defaults option add the ea variable in twig, needed to extend easyadmin templates
    // see https://github.com/EasyCorp/EasyAdminBundle/pull/6765
    // NB: this was not necessary with the old ea routing strategy
    #[Route('/story/{id:story}/media/bulk-remove-tags', name: 'admin_media_bulk_remove_tags_by_story', methods: ['GET', 'POST'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function bulkRemoveTagsByStory(Story $story, Request $request, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        $form = $formFactory->createNamed(
            name: 'media_bulk_remove_tags_form_' . $story->getId(),
            type: MediaBulkEditTagsType::class,
            options: ['story' => $story],
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->_processBulkRemoveTagsForm($form, $story);
           
            $entityManager->flush();

            $this->addFlash('success', 'Tag removed successfully');
        }

        return $this->redirectToRoute('admin_media_bulk_edit_by_story', [
            'id' => $story->getId(),
        ]);
    }

    // EA defaults option add the ea variable in twig, needed to extend easyadmin templates
    // see https://github.com/EasyCorp/EasyAdminBundle/pull/6765
    // NB: this was not necessary with the old ea routing strategy
    #[Route('/food/{id:food}/media/bulk-edit', name: 'admin_media_bulk_edit_by_food', methods: ['GET', 'POST'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function bulkEditByFood(Food $food, Request $request, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        $foodForm = $formFactory->createNamed(
            // form names must match with story quick edit form
            name: 'food_quick_edit_form_' . $food->getId(),
            type: FoodQuickEditType::class,
            data: $food,
        );

        $medias = $entityManager->getRepository(Media::class)->findByFood($food);

        $forms = [];
        foreach ($medias as $media) {
            $forms[$media->getId()] = $formFactory->createNamed(
                // form names must match with media quick edit form
                name: 'media_quick_edit_form_' . $media->getId(),
                type: MediaQuickEditType::class,
                data: $media,
                options: ['trip' => $media->getTrip()]
            )->createView();
        }

        return $this->render('@admin/media/bulk_edit_by_food.html.twig', [
            'food_form' => $foodForm,
            'forms' => $forms,
            'medias' => $medias,
            'food' => $food,
        ]);
    }

    private function _processBulkAddTagsForm(FormInterface $form, Story $story)
    {
        $tags = $form['tags']->getData();
        $placeTags = $form['placeTags']->getData();

        foreach ($story->getMedias() as $media) {
            foreach ($tags as $tag) {
                $media->addTag($tag);
            }
            foreach ($placeTags as $placeTag) {
                $media->addPlaceTag($placeTag);
            }
        }
    }

    private function _processBulkRemoveTagsForm(FormInterface $form, Story $story)
    {
        $tags = $form['tags']->getData();
        $placeTags = $form['placeTags']->getData();

        foreach ($story->getMedias() as $media) {
            foreach ($tags as $tag) {
                $media->removeTag($tag);
            }
            foreach ($placeTags as $placeTag) {
                $media->removePlaceTag($placeTag);
            }
        }
    }

}