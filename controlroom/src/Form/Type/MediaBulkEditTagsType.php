<?php

namespace Controlroom\Form\Type;

use App\Entity\Place;
use App\Entity\Tag\MediaTag;
use App\Entity\Tag\PlaceTag;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaBulkEditTagsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $story = $options['story'];
        $trip = $story->getTrip();

        $builder
            ->add('placeTags', EntityType::class, [
                'class' => PlaceTag::class,
                'query_builder' => function (EntityRepository $repo) use ($trip): QueryBuilder {
                    return $repo->createQueryBuilder('pt')
                        ->where('pt.country IN (:countries)')
                        ->setParameter('countries', $trip->getCountries())
                        ->orderBy('pt.nameEn', 'ASC');
                },
                'required' => false,
                'multiple' => true,
                'autocomplete' => true,
                // 'by_reference' => false, // not needed here. Form is not tied to a class, we handle it by hand in MediuBulkEditController
            ])
            ->add('tags', EntityType::class, [
                'class' => MediaTag::class,
                'query_builder' => function (EntityRepository $repo): QueryBuilder {
                    return $repo->createQueryBuilder('t')
                        ->orderBy('t.nameEn', 'ASC');
                },
                'required' => false,
                'multiple' => true,
                'autocomplete' => true,
                // 'by_reference' => false, // not needed here. Form is not tied to a class, we handle it by hand in MediuBulkEditController
        ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'query_builder' => function (EntityRepository $repo) use ($trip): QueryBuilder {
                    return $repo->createQueryBuilder('p')
                        ->where('p.country IN (:countries)')
                        ->setParameter('countries', $trip->getCountries())
                        ->orderBy('p.name', 'ASC');
                },
                'required' => false,
                'multiple' => false,
                'autocomplete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'story' => null,
        ]);
    }
}
