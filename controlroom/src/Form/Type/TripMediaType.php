<?php

namespace Controlroom\Form\Type;

use App\Entity\Food;
use App\Entity\Meal;
use App\Entity\Media;
use App\Entity\Tag\MediaTag;
use App\Entity\Tag\PlaceTag;
use App\Entity\Trip;
use App\Entity\TripHighlight;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentTrip = $options['trip'];

        $builder
            ->add('trip', EntityType::class, [
                'class' => Trip::class,
                'query_builder' => function (EntityRepository $repo) use ($currentTrip): QueryBuilder {
                    return $repo->createQueryBuilder('t')
                        ->where('t.parentTrip = :trip')
                        ->setParameter('trip', $currentTrip)
                        ->orWhere('t.id = :tripId')
                        ->setParameter('tripId', $currentTrip->getId())
                        ->orderBy('t.startedAt', 'DESC');
                },
                'multiple' => false,
                'autocomplete' => true,
            ])
            ->add('tripHighlight', EntityType::class, [
                'label' => 'Highlight',
                'class' => TripHighlight::class,
                'required' => false,
                'query_builder' => function (EntityRepository $repo) use ($currentTrip): QueryBuilder {
                    return $repo->createQueryBuilder('h')
                        ->where('h.trip = :trip')
                        ->setParameter('trip', $currentTrip)
                        ->orderBy('h.nameEn', 'ASC');
                },
                'multiple' => false,
                'autocomplete' => true,
            ])
            ->add('descriptionEn', TextareaType::class, [
                'label' => "EN",
                'required' => false,
                'attr' => [
                    'rows' => 4,      // taller
                ],
            ])
            ->add('descriptionFr', TextareaType::class, [
                'label' => "FR",
                'required' => false,
                'attr' => [
                    'rows' => 4,      // taller
                ],
            ])
            ->add('placeTags', EntityType::class, [
                'class' => PlaceTag::class,
                'query_builder' => function (EntityRepository $repo): QueryBuilder {
                    return $repo->createQueryBuilder('t')
                        ->orderBy('t.nameEn', 'ASC');
                },
                'required' => false,
                'multiple' => true,
                'autocomplete' => true,
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
            ])
            ->add('food', EntityType::class, [
                'class' => Food::class,
                'query_builder' => function (EntityRepository $repo): QueryBuilder {
                    return $repo->createQueryBuilder('f')
                        ->orderBy('f.nameEn', 'ASC');
                },
                'required' => false,
                'multiple' => false,
                'autocomplete' => true,
            ])
            ->add('meal', EntityType::class, [
                'class' => Meal::class,
                'query_builder' => function (EntityRepository $repo): QueryBuilder {
                    return $repo->createQueryBuilder('m')
                        ->orderBy('m.enjoyedAt', 'ASC');
                },
                'required' => false,
                'multiple' => false,
                'autocomplete' => true,
            ])
            ->add('isMeal', CheckboxType::class, [
                'label'    => '',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
            'trip' => null,
        ]);
    }
}
