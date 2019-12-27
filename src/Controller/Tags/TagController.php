<?php

namespace App\Controller\Tags;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tag;

class TagController extends AbstractController
{
    /**
     * @Route("/tags", name="tag_index")
     */
    public function index(EntityManagerInterface $em)
    {
        $repository = $em->getRepository(Tag::class);
        $tags = $repository->findAll();

        return $this->render('tags/tag/index.html.twig', [
            'tags' => $tags,
        ]);
    }

    /**
     * @Route("/tag/{slug}", name="tag_show")
     */
    public function show($slug, EntityManagerInterface $em)
    {
        $repository = $em->getRepository(Tag::class);
        $tag = $repository->findOneBySlug($slug);

        return $this->render('tags/tag/show.html.twig', [
            'tag' => $tag,
        ]);
    }

}
