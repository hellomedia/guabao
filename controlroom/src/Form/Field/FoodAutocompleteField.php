<?php

namespace Controlroom\Form\Field;

use App\Entity\Food;
use App\Repository\FoodRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class FoodAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'label' => 'Food',
            'class' => Food::class,
            'placeholder' => '',
            // choose which fields to use in the search. 
            // if not passed, *all* fields are used
            // NB: relationships can be used. ie: category.name
            'searchable_fields' => ['nameEn', 'nameFr'],
            'query_builder' => function (FoodRepository $repo): QueryBuilder {
                return $repo->createQueryBuilder('f')
                    ->addOrderBy('f.nameEn', 'ASC')
                ;
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
