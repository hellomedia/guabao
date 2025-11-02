<?php

namespace Controlroom\Controller;

use App\Controller\BaseController;
use App\Entity\Trip;
use Controlroom\Form\Type\TripQuickEditType;
use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    #[Route('/trip/{id:trip}/quick-edit', name: 'admin_trip_quick_edit', methods: ['POST'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function quickEdit(Request $request, Trip $trip, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
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