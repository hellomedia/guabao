<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Video;

class VideoController extends AbstractController
{
    /**
     * @Route("/videos", name="video_index")
     */
    public function index(EntityManagerInterface $em)
    {
        $repository = $em->getRepository(Video::class);
        $videos = $repository->findAll();

        return $this->render('video/index.html.twig', [
            'videos' => $videos,
        ]);
    }

    /**
     * @Route("/video/{slug}", name="video_show")
     */
    public function show($slug, EntityManagerInterface $em)
    {
        $repository = $em->getRepository(Video::class);
        $video = $repository->findOneBySlug($slug);

        return $this->render('video/show.html.twig', [
            'video' => $video,
        ]);
    }

}
