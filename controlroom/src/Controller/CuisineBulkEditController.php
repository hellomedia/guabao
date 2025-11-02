<?php

namespace Controlroom\Controller;

use App\Controller\BaseController;
use App\Entity\Cuisine;
use Controlroom\Form\Type\CuisineQuickEditType;
use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CuisineBulkEditController extends BaseController
{
    public function __construct(
        private EntityManager $entityManager,
    )
    {
    }

    // EA defaults option add the ea variable in twig, needed to extend easyadmin templates
    // see https://github.com/EasyCorp/EasyAdminBundle/pull/6765
    // NB: this was not necessary with the old ea routing strategy
    #[Route('/cuisine/bulk-edit', name: 'admin_cuisine_bulk_edit', methods: ['GET'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function bulkEdit(EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        $cuisines = $entityManager->getRepository(Cuisine::class)->findAll();

        $forms = [];
        foreach ($cuisines as $cuisine) {
            $forms[$cuisine->getId()] = $formFactory->createNamed(
                // form names must match between bulk edit forms and ajax edit form
                name: 'cuisine_quick_edit_form_' . $cuisine->getId(),
                type: CuisineQuickEditType::class,
                data: $cuisine,
            )->createView();
        }

        return $this->render('@admin/cuisine/bulk_edit.html.twig', [
            'forms' => $forms,
            'cuisines' => $cuisines,
        ]);
    }

    /**
     * Called in ajax from bulk edit forms
     * 
     * Route must not conflict with easyadmin /cuisine/{id}/edit
     */
    #[Route('/cuisine/{id:cuisine}/quick-edit', name: 'admin_cuisine_quick_edit', methods: ['POST'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function quickEdit(Request $request, Cuisine $cuisine, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        // form names must match with batch edit forms
        $form = $formFactory->createNamed(
            name: 'cuisine_quick_edit_form_' . $cuisine->getId(),
            type: CuisineQuickEditType::class,
            data: $cuisine,
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cuisine);
            $entityManager->flush();
        }

        return $this->render('@admin/cuisine/_quick_edit_form.html.twig', [
            'form' => $form,
            'cuisine' => $cuisine,
        ]);
    }
}