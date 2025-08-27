<?php

namespace Controlroom\Controller;

use App\Controller\BaseController;
use App\Entity\Ingredient;
use Controlroom\Form\Type\IngredientQuickEditType;
use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientBulkEditController extends BaseController
{
    public function __construct(
        private EntityManager $entityManager,
    )
    {
    }

    // EA defaults option add the ea variable in twig, needed to extend easyadmin templates
    // see https://github.com/EasyCorp/EasyAdminBundle/pull/6765
    // NB: this was not necessary with the old ea routing strategy
    #[Route('/ingredient/bulk-edit', name: 'admin_ingredient_bulk_edit', methods: ['GET'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function bulkEdit(EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        $ingredients = $entityManager->getRepository(Ingredient::class)->findAll();

        $forms = [];
        foreach ($ingredients as $ingredient) {
            $forms[$ingredient->getId()] = $formFactory->createNamed(
                // form names must match between bulk edit forms and ajax edit form
                name: 'ingredient_quick_edit_form_' . $ingredient->getId(),
                type: IngredientQuickEditType::class,
                data: $ingredient,
            )->createView();
        }

        return $this->render('@controlroom/ingredient/bulk_edit.html.twig', [
            'forms' => $forms,
            'ingredients' => $ingredients,
        ]);
    }

    /**
     * Called in ajax from bulk edit forms
     * 
     * Route must not conflict with easyadmin /ingredient/{id}/edit
     */
    #[Route('/ingredient/{id:ingredient}/quick-edit', name: 'admin_ingredient_quick_edit', methods: ['POST'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function quickEdit(Request $request, Ingredient $ingredient, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        // form names must match with batch edit forms
        $form = $formFactory->createNamed(
            name: 'ingredient_quick_edit_form_' . $ingredient->getId(),
            type: IngredientQuickEditType::class,
            data: $ingredient,
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ingredient);
            $entityManager->flush();
        }

        return $this->render('@controlroom/ingredient/_quick_edit_form.html.twig', [
            'form' => $form,
            'ingredient' => $ingredient,
        ]);
    }
}