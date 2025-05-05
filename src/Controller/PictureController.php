<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Repository\PictureRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PictureController extends BaseController
{
    #[Route('/picture', name: 'picture_index')]
    public function index(PictureRepository $pictureRepository): Response
    {
        $pictures = $pictureRepository->findAll();
        
        return $this->render('picture/index.html.twig', [
            'pictures' => $pictures
        ]);
    }

    #[Route('/picture/{id:picture}', name: 'picture_show')]
    public function show(Picture $picture): Response
    {
        return $this->render('picture/show.html.twig', [
            'picture' => $picture
        ]);
    }
}
