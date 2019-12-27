<?php

namespace App\Controller\Tags;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Person;

class PersonController extends AbstractController
{
    /**
     * @Route("/people", name="person_index")
     */
    public function index(EntityManagerInterface $em)
    {
        $repository = $em->getRepository(Person::class);
        $people = $repository->findAll();

        return $this->render('tags/person/index.html.twig', [
            'people' => $people,
        ]);
    }

    /**
     * @Route("/person/{slug}", name="person_show")
     */
    public function show($slug, EntityManagerInterface $em)
    {
        $repository = $em->getRepository(Person::class);
        $person = $repository->findOneBySlug($slug);

        return $this->render('tags/person/show.html.twig', [
            'person' => $person,
        ]);
    }

}
