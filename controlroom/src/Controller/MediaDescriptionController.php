<?php

namespace Controlroom\Controller;

use App\Controller\BaseController;
use App\Entity\Media;
use App\Entity\Trip;
use Controlroom\Form\DTO\MediaDescriptionsDTO;
use Controlroom\Form\MediaDescriptionsForm;
use Controlroom\Form\Type\MediaDescriptionType;
use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaDescriptionController extends BaseController
{
    public function __construct(
    )
    {
    }

    // EA defaults option add the ea variable in twig, needed to extend easyadmin templates
    // see https://github.com/EasyCorp/EasyAdminBundle/pull/6765
    // NB: this was not necessary with the old ea routing strategy
    #[Route('/media/trip/{id:trip}/descriptions', name: 'admin_media_descriptions', methods: ['GET'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function editDescriptions(Trip $trip, EntityManager $entityManager, Request $request, FormFactoryInterface $formFactory): Response
    {
        $medias = $entityManager->getRepository(Trip::class)->findMedias($trip);

        $forms = [];
        foreach ($medias as $media) {
            $forms[$media->getId()] = $formFactory->createNamed('media_description_' . $media->getId(), MediaDescriptionType::class, $media)->createView();
        }

        return $this->render('@controlroom/media/edit_descriptions.html.twig', [
            'forms' => $forms,
            'medias' => $medias,
            'trip' => $trip,
        ]);

    }

    #[Route('/media/{id:media}/description', name: 'admin_media_description_edit', methods: ['POST'])]
    public function editMediaDescription(Request $request, Media $media, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        // form name must match name above
        $form = $formFactory->createNamed('media_description_' . $media->getId(), MediaDescriptionType::class, $media);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($media);
            $entityManager->flush();
        }

        // Return just the frame content so Turbo can replace it
        return $this->render('@controlroom/media/_description_form.html.twig', [
            'form' => $form,
            'media' => $media,
        ]);
    }
}