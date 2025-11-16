<?php

namespace Controlroom\Form\Field;

use App\Entity\Tag\MediaTag;
use App\Repository\MediaTagRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class MediaTagAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'label' => 'Tags',
            'class' => MediaTag::class,
            'placeholder' => '',
            // choose which fields to use in the search
            // if not passed, *all* fields are used
            'searchable_fields' => ['nameEn', 'nameFr'],
            'query_builder' => function (MediaTagRepository $repo): QueryBuilder {
                return $repo->createQueryBuilder('t')
                    ->addOrderBy('t.nameEn', 'ASC')
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
