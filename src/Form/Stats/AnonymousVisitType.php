<?php

namespace App\Form\Stats;

use App\Entity\Stats\AnonymousVisit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnonymousVisitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sessionId')
            ->add('visitorId')
            ->add('startedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('lastSeenAt', null, [
                'widget' => 'single_text',
            ])
            ->add('pageCount')
            ->add('isReturning')
            ->add('countryCode')
            ->add('cityName')
            ->add('firstPath')
            ->add('landingReferrer')
            ->add('userAgent')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AnonymousVisit::class,
        ]);
    }
}
