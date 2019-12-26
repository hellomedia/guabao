<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Level;

class LevelController extends AbstractController
{
    /**
     * @Route("/levels", name="level_index")
     */
    public function index(EntityManagerInterface $em)
    {
        $repository = $em->getRepository(Level::class);
        $levels = $repository->findAll();

        return $this->render('level/index.html.twig', [
            'levels' => $levels,
        ]);
    }

    /**
     * @Route("/level/{slug}", name="level_show")
     */
    public function show($slug, EntityManagerInterface $em)
    {
        $repository = $em->getRepository(Level::class);
        $level = $repository->findOneBySlug($slug);

        return $this->render('level/show.html.twig', [
            'level' => $level,
        ]);
    }

}
