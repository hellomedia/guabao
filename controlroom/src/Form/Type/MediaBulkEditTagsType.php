<?php

namespace Controlroom\Form\Type;

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
                // 'by_reference' => false, // not needed here. Form is not tied to a class, we handle it by hand in mediaController::_importImages
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
                // 'by_reference' => false, // not needed here. Form is not tied to a class, we handle it by hand in mediaController::_importImages
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
