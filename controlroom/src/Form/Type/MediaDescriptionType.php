<?php

namespace Controlroom\Form\Type;

use App\Entity\Food;
use App\Entity\Meal;
use App\Entity\Media;
use App\Entity\Tag\MediaTag;
use App\Entity\Tag\PlaceTag;
use App\Entity\Trip;
use App\Repository\TripRepository;
use Doctrine\ORM\QueryBuilder;
use phpDocumentor\Reflection\DocBlock\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaDescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('trip', EntityType::class, [
                'class' => Trip::class,
                'query_builder' => function (TripRepository $repo): QueryBuilder {
                    return $repo->createQueryBuilder('t')
                        ->orderBy('t.startedAt', 'DESC');
                },
                'choice_label' => function (Trip $trip): string {
                    if ($trip->hasParentTrip()) {
                        return $trip->getParentTrip()->getShortNameWithFallback() . ' ' . $trip->getName();
                    }
                    return $trip->getShortNameWithFallback();
                },
                'multiple' => false,
                'autocomplete' => true,
            ])
            ->add('descriptionFr', TextareaType::class, [
                'label' => "FR",
                'required' => false,
                'attr' => [
                    'rows' => 6,      // taller
                    'cols' => 80,     // wider (optional)
                ],
            ])
            ->add('descriptionEn', TextareaType::class, [
                'label' => "EN",
                'required' => false,
                'attr' => [
                    'rows' => 6,      // taller
                    'cols' => 80,     // wider (optional)
                ],
            ])
            ->add('placeTags', EntityType::class, [
                'class' => PlaceTag::class,
                'multiple' => true,
                'autocomplete' => true,
            ])
            ->add('tags', EntityType::class, [
                'class' => MediaTag::class,
                'multiple' => true,
                'autocomplete' => true,
            ])
            ->add('food', EntityType::class, [
                'class' => Food::class,
                'multiple' => false,
                'autocomplete' => true,
            ])
            ->add('meal', EntityType::class, [
                'class' => Meal::class,
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
        ]);
    }
}
