<?php

namespace Controlroom\Controller;

use App\Controller\BaseController;
use App\Entity\Food;
use Controlroom\Form\Type\FoodQuickEditType;
use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FoodBulkEditController extends BaseController
{
    public function __construct(
        private EntityManager $entityManager,
    )
    {
    }

    // EA defaults option add the ea variable in twig, needed to extend easyadmin templates
    // see https://github.com/EasyCorp/EasyAdminBundle/pull/6765
    // NB: this was not necessary with the old ea routing strategy
    #[Route('/food/bulk-edit', name: 'admin_food_bulk_edit', methods: ['GET'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function bulkEdit(EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        $foodList = $entityManager->getRepository(Food::class)->findAll();

        $forms = [];
        foreach ($foodList as $food) {
            $forms[$food->getId()] = $formFactory->createNamed(
                // form names must match between bulk edit forms and ajax edit form
                name: 'food_quick_edit_form_' . $food->getId(),
                type: FoodQuickEditType::class,
                data: $food,
            )->createView();
        }

        return $this->render('@controlroom/food/bulk_edit.html.twig', [
            'forms' => $forms,
            'foodList' => $foodList,
        ]);
    }

    /**
     * Called in ajax from bulk edit forms
     * 
     * Route must not conflict with easyadmin /food/{id}/edit
     */
    #[Route('/food/{id:food}/quick-edit', name: 'admin_food_quick_edit', methods: ['POST'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function quickEdit(Request $request, Food $food, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        // form names must match with batch edit forms
        $form = $formFactory->createNamed(
            name: 'food_quick_edit_form_' . $food->getId(),
            type: FoodQuickEditType::class,
            data: $food,
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($food);
            $entityManager->flush();
        }

        return $this->render('@controlroom/food/_quick_edit_form.html.twig', [
            'form' => $form,
            'food' => $food,
        ]);
    }
}