<?php

namespace Controlroom\Form\Field;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class IngredientAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'Ingredients',
            'class' => Ingredient::class,
            'placeholder' => '',
            // choose which fields to use in the search
            // if not passed, *all* fields are used
            'searchable_fields' => ['nameEn', 'nameFr'],
            'query_builder' => function (IngredientRepository $repo): QueryBuilder {
                return $repo->createQueryBuilder('i')
                    ->addOrderBy('i.nameEn', 'ASC')
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
