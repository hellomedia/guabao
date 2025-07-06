<?php

namespace Controlroom\Form\Type;

use App\Entity\Trip;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;

class MediaMultipleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraints = [
            new Count(['max' => 20]),
            new All([
                new File([
                    'maxSize' => '20M',
                    'mimeTypes' => ['image/jpeg', 'image/jpg'],
                ]),
            ]),
            new NotNull([
                'message' => 'Please upload (a) file(s)',
            ]),
        ];

        $builder
            ->add('trip', EntityType::class, [
                'class' => Trip::class,
            ])
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
            ->add('submit', SubmitType::class)
        ;
    }
}
