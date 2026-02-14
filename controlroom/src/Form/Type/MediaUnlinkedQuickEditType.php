<?php

namespace Controlroom\Form\Type;

use App\Entity\Media;
use App\Entity\SiteHighlight;
use Controlroom\Form\Field\FoodAutocompleteField;
use Controlroom\Form\Field\MealAutocompleteField;
use Controlroom\Form\Field\MediaTagAutocompleteField;
use Controlroom\Form\Field\PlaceAutocompleteField;
use Controlroom\Form\Field\PlaceTagAutocompleteField;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaUnlinkedQuickEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descriptionEn', TextareaType::class, [
                'label' => "EN",
                'required' => false,
                'attr' => [
                    'rows' => 3,
                ],
            ])
            ->add('descriptionFr', TextareaType::class, [
                'label' => "FR",
                'required' => false,
                'attr' => [
                    'rows' => 3,
                ],
            ])
            ->add('takenAt')
            ->add('siteHighlights', EntityType::class, [
                'label' => 'Highlights',
                'class' => SiteHighlight::class,
                'required' => false,
                'query_builder' => function (EntityRepository $repo) : QueryBuilder {
                    return $repo->createQueryBuilder('h')
                        ->orderBy('h.nameEn', 'ASC');
                },
                'multiple' => true,
                'autocomplete' => true,
                'by_reference' => false, // important for ManyToMany when using add/remove methods
            ])
            ->add('placeTags', PlaceTagAutocompleteField::class, [])
            ->add('tags', MediaTagAutocompleteField::class)
            ->add('food', FoodAutocompleteField::class)
            ->add('showInFood', CheckboxType::class, [
                'label'    => 'show in food meal',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
            ])
            ->add('isMeal', CheckboxType::class, [
                'label' => 'Is meal',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
                'help' => 'Set to true to auto-create meal or auto-fill with existing meal based on taken_at time +- 30 minutes'
            ])
            ->add('meal', MealAutocompleteField::class, [
                'required' => false,
                'help' => "Leave empty if using the 'set meal' flag to auto-fill or auto-create meal", 
            ])
            ->add('place', PlaceAutocompleteField::class, [
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
