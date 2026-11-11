<?php

namespace Controlroom\Controller;

use App\Controller\BaseController;
use App\Entity\Ingredient;
use Controlroom\Form\Type\IngredientQuickEditType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

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
    #[AdminRoute('/ingredient/bulk-edit', name: 'ingredient_bulk_edit', options: ['methods' => ['GET']])]
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

        return $this->render('@admin/ingredient/bulk_edit.html.twig', [
            'forms' => $forms,
            'ingredients' => $ingredients,
        ]);
    }

    /**
     * Called in ajax from bulk edit forms
     * 
     * Route must not conflict with easyadmin /ingredient/{id}/edit
     */
    #[AdminRoute('/ingredient/{id}/quick-edit', name: 'ingredient_quick_edit', options: ['methods' => ['POST']])]
    public function quickEdit(Request $request, #[MapEntity(id: 'id')] Ingredient $ingredient, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
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

        return $this->render('@admin/ingredient/_quick_edit_form.html.twig', [
            'form' => $form,
            'ingredient' => $ingredient,
        ]);
    }
}