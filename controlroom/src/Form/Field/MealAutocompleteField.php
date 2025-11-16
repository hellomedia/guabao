<?php

namespace Controlroom\Form\Field;

use App\Entity\Meal;
use App\Repository\MealRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class MealAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'Meal',
            'class' => Meal::class,
            'placeholder' => '',
            // choose which fields to use in the search
            // if not passed, *all* fields are used
            // NB: relationships can be used. ie: place.name
            'searchable_fields' => ['place.name'],
            'multiple' => false,
            // Passing extra options to ajax query builder
            // https://symfony.com/bundles/ux-autocomplete/current/index.html#passing-extra-options-to-the-ajax-powered-autocomplete
            'query_builder' => function (Options $options) {
                return function (MealRepository $repo) use ($options): QueryBuilder {

                    $currentTrip = $options['extra_options']['current_trip'] ?? null;

                    if ($currentTrip) {
                        return $repo->createQueryBuilder('m')
                            ->join('m.medias', 'media')
                            ->where('media.trip = :current_trip')
                            ->setParameter('current_trip', $currentTrip)
                            ->orderBy('m.enjoyedAt', 'ASC');
                    }

                    return $repo->createQueryBuilder('m')
                        ->orderBy('m.enjoyedAt', 'ASC');
                };
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
