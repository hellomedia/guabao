<?php

namespace Controlroom\Form\Type;

use App\Entity\Food;
use App\Entity\Meal;
use App\Entity\Media;
use App\Entity\Place;
use App\Entity\Story;
use App\Entity\Tag\MediaTag;
use App\Entity\Tag\PlaceTag;
use App\Entity\Trip;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaQuickEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentTrip = $options['trip'];

        $builder
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
            ->add('takenAt')
            ->add('trip', EntityType::class, [
                'class' => Trip::class,
                'query_builder' => function (EntityRepository $repo) use ($currentTrip): QueryBuilder {
                    return $repo->createQueryBuilder('t')
                        // show other trip legs in the trip
                        ->where('t.parent = :parent')
                        ->setParameter('parent', $currentTrip->getParent())
                        // or if top level trip, only show current trip
                        ->orWhere('t.id = :tripId')
                        ->setParameter('tripId', $currentTrip->getId())
                        ->orderBy('t.startedAt', 'DESC');
                },
                'multiple' => false,
                'autocomplete' => true,
            ])
            ->add('showInTrip', CheckboxType::class, [
                'label'    => 'show in trip',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
            ])
            ->add('isTripCover', CheckboxType::class, [
                'label' => 'trip cover',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
            ])
            ->add('story', EntityType::class, [
                'label' => 'Story',
                'class' => Story::class,
                'required' => false,
                'query_builder' => function (EntityRepository $repo) use ($currentTrip): QueryBuilder {
                    return $repo->createQueryBuilder('s')
                        ->where('s.trip = :trip')
                        ->setParameter('trip', $currentTrip)
                        ->orderBy('s.nameEn', 'ASC');
                },
                'multiple' => false,
                'autocomplete' => true,
            ])
            ->add('showInStory', CheckboxType::class, [
                'label'    => 'show in story',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
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
                'by_reference' => false, // important for ManyToMany when using add/remove methods
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
                'by_reference' => false, // important for ManyToMany when using add/remove methods
            ])
            ->add('food', EntityType::class, [
                'class' => Food::class,
                'query_builder' => function (EntityRepository $repo): QueryBuilder {
                    return $repo->createQueryBuilder('f')
                        ->orderBy('f.nameEn', 'ASC');
                },
                'required' => false,
                'multiple' => true,
                'autocomplete' => true,
                'by_reference' => false, // important for ManyToMany when using add/remove methods
            ])
            ->add('showInFood', CheckboxType::class, [
                'label'    => 'show in food',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
            ])
            ->add('isMeal', CheckboxType::class, [
                'label' => 'Is meal',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
                'help' => 'Set to true to auto-create meal or auto-fill with existing meal based on taken_at time +- 30 minutes'
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
                'help' => "Leave empty if using the 'set meal' flag to auto-fill or auto-create meal", 
            ])
            ->add('is360', CheckboxType::class, [
                'label' => '360',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'required' => false,
                'multiple' => false,
                'autocomplete' => true,
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
