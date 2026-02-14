<?php

namespace Controlroom\Form\Type;

use App\Entity\Story;
use App\Entity\Tag\MediaTag;
use App\Entity\Tag\PlaceTag;
use App\Entity\Trip;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class MediaBulkAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $story = $options['story'] ?? null;
        $trip = $story?->getTrip() ?? $options['trip'] ?? null;

        $builder = new DynamicFormBuilder($builder);

        $constraints = [
            new Count(['max' => 20]), // same as server max_file_uploads  
            new All([
                new File([
                    'maxSize' => '50M', // same as server upload_max_filesize
                    'mimeTypes' => ['image/jpeg', 'image/jpg', 'image/avif'],
                ]),
            ]),
            new NotNull([
                'message' => 'Please upload (a) file(s)',
            ]),
        ];

        $builder
            ->add('files', FileType::class, [
                    'label' => 'images',
                    'multiple' => true,
                    'required' => true,
                    'constraints' => $constraints,
                    'attr' => [
                        'accept' => 'image/*',
                    ],
                ]
            )
            ->add('trip', EntityType::class, [
                'class' => Trip::class,
                'query_builder' => function (EntityRepository $repo): QueryBuilder {
                    return $repo->createQueryBuilder('t')
                        ->orderBy('t.startedAt', 'DESC');
                },
                'required' => false,
                'multiple' => false,
                'autocomplete' => true,
                'group_by' => function (Trip $trip, $key, $value) {
                    if ($trip->hasParent()) {
                        return $trip->getParent()->getNameEn();
                    }
                    return $trip->getNameEn();
                },
                'data' => $trip,
                'placeholder' => '', // add an empty placeholder instead of a default trip selected
            ])
            ->add('showInTrip', CheckboxType::class, [
                'label'    => 'show in trip',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
            ])
            ->addDependent('story', 'trip', function(DependentField $field, ?Trip $selectedTrip) use ($story) {
                if ($selectedTrip === null) {
                    return;
                }
                // Add **non-ajax** autocomplete since this is already a dependent field with ajax update
                $field->add(EntityType::class, [
                    'required' => false,
                    'class' => Story::class,
                    'query_builder' => function (EntityRepository $repo) use ($selectedTrip): QueryBuilder {
                        return $repo->createQueryBuilder('s')
                            ->where('s.trip = :trip')
                            ->setParameter('trip', $selectedTrip)
                            ->orderBy('s.displayOrder', 'ASC');
                    },
                    'multiple' => false,
                    'autocomplete' => true,
                    'data' => $story,
                ]);
            })
            ->addDependent('showInStory', 'trip', function (DependentField $field, ?Trip $selectedTrip) use ($story){
                if ($selectedTrip === null) {
                    return;
                }
                $field->add(CheckboxType::class, [
                    'label' => 'show in story',
                    'required' => false,
                    'data' => $story !== null,
                    'row_attr' => ['class' => 'form-switch'], // for switch
                    'attr' => ['class' => 'form-check-input'], // for switch
                ]);
            })
            ->add('placeTags', EntityType::class, [
                'class' => PlaceTag::class,
                'query_builder' => function (EntityRepository $repo) use ($trip): QueryBuilder {
                    if ($trip) {
                        return $repo->createQueryBuilder('pt')
                            ->where('pt.country IN (:countries)')
                            ->setParameter('countries', $trip->getCountries())
                            ->orderBy('pt.nameEn', 'ASC');
                    }
                    return $repo->createQueryBuilder('pt')
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
            ->add('showInFood', CheckboxType::class, [
                'label'    => 'show in food meal',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'trip' => null,
            'story' => null,
        ]);
    }
}
