<?php

namespace Controlroom\Form\Field;

use App\Entity\Place;
use App\Repository\PlaceRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class PlaceAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'label' => 'Place',
            'class' => Place::class,
            'placeholder' => '',
            // choose which fields to use in the search
            // if not passed, *all* fields are used
            'searchable_fields' => ['name'],
            'query_builder' => function (Options $options) {
                return function (PlaceRepository $repo) use ($options): QueryBuilder {

                    $countries = $options['extra_options']['countries'] ?? null;

                    if ($countries) {
                        return $repo->createQueryBuilder('p')
                            ->where('p.country IN (:countries)')
                            ->setParameter('countries', $countries)
                            ->orderBy('p.name', 'ASC');
                    }
                
                    return $repo->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
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
