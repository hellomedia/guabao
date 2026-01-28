<?php

namespace Controlroom\Form\Type;

use App\Entity\Country;
use App\Entity\Media;
use App\Entity\SiteHighlight;
use App\Entity\Trip;
use Controlroom\Form\Field\FoodAutocompleteField;
use Controlroom\Form\Field\MealAutocompleteField;
use Controlroom\Form\Field\MediaTagAutocompleteField;
use Controlroom\Form\Field\PlaceAutocompleteField;
use Controlroom\Form\Field\PlaceTagAutocompleteField;
use Controlroom\Form\Field\StoryAutocompleteField;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaQuickEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentTrip = $options['trip'];
        \assert($currentTrip instanceof Trip);
        $countries = $currentTrip->getCountries();

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
            ->add('trip', EntityType::class, [
                'class' => Trip::class,
                'query_builder' => function (EntityRepository $repo) use ($currentTrip): QueryBuilder {
                    return $repo->createQueryBuilder('t')
                        // show other trip legs in the trip
                        ->where('t.parent = :parent')
                        ->setParameter('parent', $currentTrip->getParent())
                        // or if top level trip show current trip
                        ->orWhere('t.id = :tripId')
                        ->setParameter('tripId', $currentTrip->getId())
                        // of show children
                        ->orWhere('t.id IN (:children)')
                        ->setParameter('children', $currentTrip->getChildren())
                        ->orderBy('t.startedAt', 'DESC');
                },
                'multiple' => false,
                'autocomplete' => true,
            ])
            ->add('showInTrip', CheckboxType::class, [
                'label'    => 'show in trip',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
            ])
            ->add('isTripCover', CheckboxType::class, [
                'label' => 'trip cover',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
            ])
            ->add('story', StoryAutocompleteField::class, [
                'required' => false,
                // Passing extra options to ajax query builder
                // https://symfony.com/bundles/ux-autocomplete/current/index.html#passing-extra-options-to-the-ajax-powered-autocomplete
                'extra_options' => [
                    'current_trip' => $currentTrip->getId(), // only scalar values and arrays of scalars
            ],
            ])
            ->add('showInStory', CheckboxType::class, [
                'label'    => 'show in story',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
                ])
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
            ->add('placeTags', PlaceTagAutocompleteField::class, [
                // Passing extra options to ajax query builder
                // https://symfony.com/bundles/ux-autocomplete/current/index.html#passing-extra-options-to-the-ajax-powered-autocomplete
                'extra_options' => [
                    'countries' => $countries->map(fn(Country $country) => $country->getId())->toArray(), // only scalar values and arrays of scalars
            ],
            ])
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
                // Passing extra options to ajax query builder
                // https://symfony.com/bundles/ux-autocomplete/current/index.html#passing-extra-options-to-the-ajax-powered-autocomplete
                'extra_options' => [
                    'current_trip' => $currentTrip->getId(), // only scalar values and arrays of scalars
                ],
                'help' => "Leave empty if using the 'set meal' flag to auto-fill or auto-create meal", 
            ])
            ->add('is360', CheckboxType::class, [
                'label' => '360',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
            ])
            ->add('isPano', CheckboxType::class, [
                'label' => 'Pano',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
            ])
            ->add('size2', CheckboxType::class, [
                'label'    => 'size 2',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
            ])
            ->add('size3', CheckboxType::class, [
                'label'    => 'size 3',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
            ])
            ->add('isPrimaryVideo', CheckboxType::class, [
                'label'    => 'Primary vid',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
            ])
            ->add('place', PlaceAutocompleteField::class, [
                // Passing extra options to ajax query builder
                // https://symfony.com/bundles/ux-autocomplete/current/index.html#passing-extra-options-to-the-ajax-powered-autocomplete
                'extra_options' => [
                    'countries' => $countries->map(fn(Country $country) => $country->getId())->toArray(), // only scalar values and arrays of scalars
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
            'trip' => null,
        ]);
    }
}
