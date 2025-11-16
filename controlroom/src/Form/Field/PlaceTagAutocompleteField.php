<?php

namespace Controlroom\Form\Field;

use App\Entity\Tag\PlaceTag;
use App\Repository\PlaceTagRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class PlaceTagAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'label' => 'Place tags',
            'class' => PlaceTag::class,
            'placeholder' => '',
            // choose which fields to use in the search
            // if not passed, *all* fields are used
            'searchable_fields' => ['nameEn', 'nameFr'],
            'query_builder' => function (Options $options) {
                return function (PlaceTagRepository $repo) use ($options): QueryBuilder {

                    $countries = $options['extra_options']['countries'] ?? null;

                    if ($countries) {
                        return $repo->createQueryBuilder('pt')
                            ->where('pt.country IN (:countries)')
                            ->setParameter('countries', $countries)
                            ->orderBy('pt.nameEn', 'ASC');
                    }
                
                    return $repo->createQueryBuilder('pt')
                        ->orderBy('pt.nameEn', 'ASC');
                };
            },
            'multiple' => true,
            'by_reference' => false, // important for ManyToMany when using add/remove methods
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
