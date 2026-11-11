<?php

namespace Controlroom\Controller;

use App\Controller\BaseController;
use App\Entity\Trip;
use Controlroom\Form\Type\TripQuickEditType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

class TripController extends BaseController
{
    public function __construct(
        private EntityManager $entityManager,
    )
    {
    }

    /**
     * Route must not conflict with easyadmin /trip/{id}/edit
     */
    #[AdminRoute('/trip/{id}/quick-edit', name: 'trip_quick_edit', options: ['methods' => ['POST']])]
    public function quickEdit(Request $request, #[MapEntity(id: 'id')] Trip $trip, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        // form names must match between batch edit forms and ajax edit form
        $form = $formFactory->createNamed(
            name: 'trip_quick_edit_form_' . $trip->getId(),
            type: TripQuickEditType::class,
            data: $trip,
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($trip);
            $entityManager->flush();
        }

        return $this->render('@admin/trip/_quick_edit_form.html.twig', [
            'form' => $form,
            'trip' => $trip,
        ]);
    }
}