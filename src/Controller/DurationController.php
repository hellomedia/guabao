<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Duration;

class DurationController extends AbstractController
{
    /**
     * @Route("/durations", name="duration_index")
     */
    public function index(EntityManagerInterface $em)
    {
        $repository = $em->getRepository(Duration::class);
        $durations = $repository->findAll();

        return $this->render('duration/index.html.twig', [
            'durations' => $durations,
        ]);
    }

    /**
     * @Route("/duration/{slug}", name="duration_show")
     */
    public function show($slug, EntityManagerInterface $em)
    {
        $repository = $em->getRepository(Duration::class);
        $duration = $repository->findOneBySlug($slug);

        return $this->render('duration/show.html.twig', [
            'duration' => $duration,
        ]);
    }

}
