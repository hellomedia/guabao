<?php

namespace App\Controller\Tags;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Channel;

class ChannelController extends AbstractController
{
    /**
     * @Route("/channels", name="channel_index")
     */
    public function index(EntityManagerInterface $em)
    {
        $repository = $em->getRepository(Channel::class);
        $channels = $repository->findAll();

        return $this->render('tags/channel/index.html.twig', [
            'channels' => $channels,
        ]);
    }

    /**
     * @Route("/channel/{slug}", name="channel_show")
     */
    public function show($slug, EntityManagerInterface $em)
    {
        $repository = $em->getRepository(Channel::class);
        $channel = $repository->findOneBySlug($slug);

        return $this->render('tags/channel/show.html.twig', [
            'channel' => $channel,
        ]);
    }

}
