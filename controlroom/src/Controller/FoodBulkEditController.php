<?php

namespace Controlroom\Controller;

use App\Controller\BaseController;
use App\Entity\Food;
use Controlroom\Form\Type\FoodQuickEditType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

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
    #[AdminRoute('/food/bulk-edit', name: 'food_bulk_edit', options: ['methods' => ['GET']])]
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

        return $this->render('@admin/food/bulk_edit.html.twig', [
            'forms' => $forms,
            'foodList' => $foodList,
        ]);
    }

    /**
     * Called in ajax from bulk edit forms
     * 
     * Route must not conflict with easyadmin /food/{id}/edit
     */
    #[AdminRoute('/food/{id}/quick-edit', name: 'food_quick_edit', options: ['methods' => ['POST']])]
    public function quickEdit(Request $request, #[MapEntity(id: 'id')] Food $food, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
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

        return $this->render('@admin/food/_quick_edit_form.html.twig', [
            'form' => $form,
            'food' => $food,
        ]);
    }
}