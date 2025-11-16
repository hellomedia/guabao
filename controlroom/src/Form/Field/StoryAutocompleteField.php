<?php

namespace Controlroom\Form\Field;

use App\Entity\Story;
use App\Repository\StoryRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class StoryAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'Story',
            'class' => Story::class,
            'placeholder' => '',
            // choose which fields to use in the search
            // if not passed, *all* fields are used
            // NB: relationships can be used. ie: category.name
            'searchable_fields' => ['nameEn'],
            // Passing extra options to ajax query builder
            // https://symfony.com/bundles/ux-autocomplete/current/index.html#passing-extra-options-to-the-ajax-powered-autocomplete
            'query_builder' => function (Options $options) {
                return function (StoryRepository $repo) use ($options): QueryBuilder {

                    $currentTrip = $options['extra_options']['current_trip'] ?? null;

                    if ($currentTrip) {
                        return $repo->createQueryBuilder('s')
                            ->where('s.trip = :trip')
                            ->setParameter('trip', $currentTrip)
                            ->orderBy('s.displayOrder', 'ASC');
                    }

                    return $repo->createQueryBuilder('s');
                };
            },
            'multiple' => false,
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
