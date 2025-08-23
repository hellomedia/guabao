<?php

namespace Controlroom\Form\Type;

use App\Entity\Story;
use App\Entity\Trip;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class MediaAddMultipleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
                'required' => true,
                'multiple' => false,
                'autocomplete' => true,
                'group_by' => function (Trip $trip, $key, $value) {
                    if ($trip->hasParent()) {
                        return $trip->getParent()->getNameEn();
                    }
                    return $trip->getNameEn();
                },
                'placeholder' => '', // add an empty placeholder instead of a default trip selected
            ])
            ->addDependent('story', 'trip', function(DependentField $field, ?Trip $trip) {
                if ($trip === null) {
                    return;
                }
                $field->add(EntityType::class, [
                    'required' => false,
                    'class' => Story::class,
                    'query_builder' => function (EntityRepository $repo) use ($trip): QueryBuilder {
                        return $repo->createQueryBuilder('s')
                            ->where('s.trip = :trip')
                            ->setParameter('trip', $trip)
                            ->orderBy('s.displayOrder', 'ASC');
                    },
                    'multiple' => false,
                    'autocomplete' => true,
                    'placeholder' => '', // add an empty placeholder instead of a default trip selected
                ]);
            })
            ->add('submit', SubmitType::class)
        ;
    }
}
