<?php

namespace App\Controller;

use App\Entity\Media;
use App\Repository\MediaRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MediaController extends BaseController
{
    #[Route('/media', name: 'media_index')]
    public function index(MediaRepository $mediaRepository): Response
    {
        $medias = $mediaRepository->findAll();
        
        return $this->render('media/index.html.twig', [
            'medias' => $medias
        ]);
    }

    #[Route('/media/{id:media}', name: 'media_show')]
    public function show(Media $media): Response
    {
        return $this->render('media/show.html.twig', [
            'media' => $media
        ]);
    }
}
