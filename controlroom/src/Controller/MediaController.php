<?php

namespace Controlroom\Controller;

use App\Controller\BaseController;
use App\Entity\Media;
use App\Entity\Story;
use App\Entity\Trip;
use App\Enum\MediaType;
use App\Helper\MediaAutoFillHelper;
use Pack\Media\Helper\ExifExtractor;
use Pack\Media\Helper\UploadHelper;
use Controlroom\Form\Type\MediaBulkAddType;
use Controlroom\Form\Type\MediaQuickEditType;
use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
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

    /**
     * Called in ajax from bulk edit forms
     * 
     * Route must not conflict with easyadmin /media/{id}/edit
     */
    #[Route('/media/{id:media}/quick-edit', name: 'admin_media_quick_edit', methods: ['POST'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function quickEdit(Request $request, Media $media, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        // form names must match with batch edit forms
        $form = $formFactory->createNamed(
            name: 'media_quick_edit_form_' . $media->getId(),
            type: MediaQuickEditType::class,
            data: $media,
            options: ['trip' => $media->getTrip()]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->_updateAutoFieldsAtQuickEdit($media);
            
            $entityManager->persist($media);
            $entityManager->flush();

            // recreate form to include modifications for auto-updates above
            $form = $formFactory->createNamed(
                name: 'media_quick_edit_form_' . $media->getId(),
                type: MediaQuickEditType::class,
                data: $media,
                options: ['trip' => $media->getTrip()]
            );
        }

        return $this->render('@admin/media/_quick_edit_form.html.twig', [
            'form' => $form,
            'media' => $media,
        ]);
    }

    #[Route('/media/bulk-add', name: 'admin_media_bulk_add', methods: ['GET', 'POST'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function bulkAdd(Request $request, EntityManager $entityManager): Response
    {
        if ($request->query->has('trip')) {
            $trip = $entityManager->getRepository(Trip::class)->find($request->query->get('trip'));
        }

        if ($request->query->has('story')) {
            $story = $entityManager->getRepository(Story::class)->find($request->query->get('story'));
        }

        $form = $this->createForm(MediaBulkAddType::class, options: ['trip' => $trip ?? null, 'story' => $story ?? null]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $submitBtn = $form->get('submit');
            assert($submitBtn instanceof ClickableInterface);

            if (!$submitBtn->isClicked()) {
                // on change event for dependent field
                return $this->render('@admin/media/bulk_add.html.twig', [
                    'form' => $form,
                ]);
            }
    
            $this->_importImages($form);

            return $this->redirectToRoute('admin_media_bulk_edit_by_trip', [
                'id' => $form['trip']->getData()->getId(),
            ]);
        }

        return $this->render('@admin/media/bulk_add.html.twig', [
            'form' => $form,
        ]);
    }

    private function _importImages(FormInterface $form)
    {
        $uploadedFiles = $form['files']->getData();
        $trip = $form['trip']->getData();
        $story = $form['story']->getData();
        $tags = $form['tags']->getData();
        $placeTags = $form['placeTags']->getData();
        $showInTrip = $form['showInTrip']->getData();
        $showInStory = $form['showInStory']->getData();
        $showInFood = $form['showInFood']->getData();

        foreach ($uploadedFiles as $uploadedFile) {

            $media = new Media();

            $media->setType(MediaType::IMAGE);

            // extract exif before converting to avif (exif lost in conversion)
            $exif = $this->exifExtractor->extractExifData($uploadedFile);

            $this->uploadHelper->uploadImage($media, $uploadedFile, resize: true);

            $media->setTrip($trip);
            $media->setShowInTrip($showInTrip);

            if ($story !== null && $trip === $story->getTrip()) {
                $media->setStory($story);
                $media->setShowInStory($showInStory);
            }

            foreach ($tags as $tag) {
                $media->addTag($tag);
            }

            foreach ($placeTags as $placeTag) {
                $media->addPlaceTag($placeTag);
            }

            $media->setShowInFood($showInFood);

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

        $this->autoFillHelper->autoAssignMeal($media);
    }

    private function _updateAutoFieldsAtQuickEdit(Media $media)
    {
        $this->autoFillHelper->autoAssignPlace($media);

        $this->autoFillHelper->autoAssignMeal($media);
    }

}