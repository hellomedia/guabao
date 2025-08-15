<?php

namespace Controlroom\Controller;

use App\Controller\BaseController;
use App\Entity\Media;
use App\Entity\Story;
use App\Entity\Trip;
use App\Enum\MediaType;
use App\Helper\MediaAutoFillHelper;
use App\Pack\Media\Helper\ExifExtractor;
use App\Pack\Media\Helper\UploadHelper;
use Controlroom\Form\Type\MediaMultipleType;
use Controlroom\Form\Type\TripMediaType;
use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends BaseController
{
    public function __construct(
        private UploadHelper $uploadHelper,
        private ExifExtractor $exifExtractor,
        private MediaAutoFillHelper $autoFillHelper,
        private EntityManager $entityManager,
    )
    {
    }

    // EA defaults option add the ea variable in twig, needed to extend easyadmin templates
    // see https://github.com/EasyCorp/EasyAdminBundle/pull/6765
    // NB: this was not necessary with the old ea routing strategy
    #[Route('/media/trip/{id:trip}/edit/{page<\d+>?1}', name: 'admin_trip_media_batch_edit', methods: ['GET'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function batchEditTripMedias(Trip $trip, int $page, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        $queryBuilder = $entityManager->getRepository(Media::class)
            ->getFindByTripQueryBuilder($trip, onlyDefaultGallery: true);

        $pager = Pagerfanta::createForCurrentPageWithMaxPerPage(
            adapter: new QueryAdapter($queryBuilder),
            currentPage: $page,
            maxPerPage: 50,
        );

        $forms = [];
        foreach ($pager as $media) {
            $forms[$media->getId()] = $formFactory->createNamed(
                // form names must match between batch edit forms and ajax edit form
                name: 'trip_media_edit_form_' . $media->getId(),
                type: TripMediaType::class,
                data: $media,
                options: ['trip' => $trip]
            )->createView();
        }

        return $this->render('@controlroom/media/trip_media_batch_edit.html.twig', [
            'forms' => $forms,
            'pager' => $pager,
            'trip' => $trip,
        ]);

    }

    // EA defaults option add the ea variable in twig, needed to extend easyadmin templates
    // see https://github.com/EasyCorp/EasyAdminBundle/pull/6765
    // NB: this was not necessary with the old ea routing strategy
    #[Route('/media/story/{id:story}/batch-edit/{page<\d+>?1}', name: 'admin_story_media_batch_edit', methods: ['GET'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function batchEditStoryMedias(Story $story, int $page, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        $queryBuilder = $entityManager->getRepository(Media::class)->getFindByStoryQueryBuilder($story);

        $pager = Pagerfanta::createForCurrentPageWithMaxPerPage(
            adapter: new QueryAdapter($queryBuilder),
            currentPage: $page,
            maxPerPage: 50,
        );

        $forms = [];
        foreach ($pager as $media) {
            $forms[$media->getId()] = $formFactory->createNamed(
                // form names must match between batch edit forms and ajax edit form
                name: 'trip_media_edit_form_' . $media->getId(),
                type: TripMediaType::class,
                data: $media,
                options: ['trip' => $story->getTrip()]
            )->createView();
        }

        return $this->render('@controlroom/media/story_media_batch_edit.html.twig', [
            'forms' => $forms,
            'pager' => $pager,
            'story' => $story,
        ]);
    }

    /**
     * Route must not conflict with easyadmin /media/{id}/edit
     */
    #[Route('/media/{id:media}/batch-edit/edit', name: 'admin_trip_media_edit', methods: ['POST'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function customEditMedia(Request $request, Media $media, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        // form names must match between batch edit forms and ajax edit form
        $form = $formFactory->createNamed(
            name: 'trip_media_edit_form_' . $media->getId(),
            type: TripMediaType::class,
            data: $media,
            options: ['trip' => $media->getTrip()]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($media);
            $entityManager->flush();
        }

        return $this->render('@controlroom/media/_batch_media_edit_form.html.twig', [
            'form' => $form,
            'media' => $media,
        ]);
    }

    #[Route('/media/add-multiple', name: 'admin_media_add_multiple', methods: ['GET', 'POST'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function addMultiple(Request $request): Response
    {
        $form = $this->createForm(MediaMultipleType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $files = $form['files']->getData();
            $trip = $form['trip']->getData();

            $this->_importImages($files, $trip);

            return $this->redirectToRoute('admin_trip_media_batch_edit', [
                'id' => $trip->getId(),
            ]);
        }

        return $this->render('@controlroom/media/add_multiple.html.twig', [
            'form' => $form,
        ]);
    }

    private function _importImages(array $uploadedFiles, Trip $trip)
    {
        foreach ($uploadedFiles as $uploadedFile) {

            $media = new Media();

            $media->setType(MediaType::IMAGE);

            // extract exif before converting to avif (exif lost in conversion)
            $exif = $this->exifExtractor->extractExifData($uploadedFile);

            $this->uploadHelper->uploadImage($media, $uploadedFile, resize: true);

            $media->setTrip($trip);

            $this->_updateAutoFields($media, $exif);

            $this->entityManager->persist($media);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Image successfully imported');
        }
    }

    private function _updateAutoFields(Media $media, array|false $exif)
    {
        $this->autoFillHelper->setTakenAt($media, $exif);

        $this->autoFillHelper->setCoordinates($media, $exif);

        // currently no place fixtures, so nothing in the DB to link to,
        // but if we do add place fixtures
        // we could query the DB  as long as PlaceFixtures is added to the dependencies
        $this->autoFillHelper->autoAssignPlace($media);
    }

}