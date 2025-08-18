<?php

namespace Controlroom\Controller;

use App\Controller\BaseController;
use App\Entity\Story;
use Controlroom\Form\Type\StoryQuickEditType;
use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StoryController extends BaseController
{
    public function __construct(
        private EntityManager $entityManager,
    )
    {
    }

    /**
     * Route must not conflict with easyadmin /story/{id}/edit
     */
    #[Route('/story/{id:story}/quick-edit', name: 'admin_story_quick_edit', methods: ['POST'], defaults: [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class])]
    public function quickEdit(Request $request, Story $story, EntityManager $entityManager, FormFactoryInterface $formFactory): Response
    {
        // form names must match between batch edit forms and ajax edit form
        $form = $formFactory->createNamed(
            name: 'story_quick_edit_form_' . $story->getId(),
            type: StoryQuickEditType::class,
            data: $story,
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($story);
            $entityManager->flush();
        }

        return $this->render('@controlroom/story/_quick_edit_form.html.twig', [
            'form' => $form,
            'story' => $story,
        ]);
    }

    #[Route('/story/reorder', name: 'admin_story_reorder', methods: ['POST'])]
    public function reorderStories(Request $request, EntityManager $entityManager): JsonResponse
    {
        $storyIds = $request->getPayload()->all('story_ids') ?? [];

        foreach ($storyIds as $displayOrder => $id) {
            $story = $entityManager->getRepository(Story::class)->find($id);
            if ($story) {
                $story->setDisplayOrder($displayOrder);
            }
        }

        $entityManager->flush();

        return new JsonResponse(['status' => 'ok']);
    }
}