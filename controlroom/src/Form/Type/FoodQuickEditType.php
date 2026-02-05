<?php

namespace Controlroom\Form\Type;

use App\Entity\Cuisine;
use App\Entity\Food;
use App\Entity\Media;
use App\Entity\SiteHighlight;
use App\Entity\Tag\FoodTag;
use App\Enum\Level;
use App\Repository\MediaRepository;
use Controlroom\Form\Field\CuisinesAutocompleteField;
use Controlroom\Form\Field\FoodTagAutocompleteField;
use Controlroom\Form\Field\IngredientAutocompleteField;
use Controlroom\Form\Field\SimilarFoodAutocompleteField;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FoodQuickEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Not best practise but OK for quickEditForm
        $food = $builder->getData();
        \assert($food instanceof Food);

        $builder
            ->add('nameEn')
            ->add('nameFr')
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
            ->add('cover', EntityType::class, [
                'class' => Media::class,
                'attr' => [
                    'data-cover-picker-target' => "input",
                ],
                'query_builder' => function (MediaRepository $repo) use ($food): QueryBuilder {
                    return $repo->createQueryBuilder('m')
                        ->innerJoin('m.food', 'f')
                        ->andWhere('f = :food')
                        ->setParameter('food', $food)
                        ->orderBy('m.id', 'ASC');
                },
            ])
            ->add('tags', FoodTagAutocompleteField::class)
            ->add('cuisines', CuisinesAutocompleteField::class)
            ->add('ingredients', IngredientAutocompleteField::class)
            ->add('similar', SimilarFoodAutocompleteField::class)
            ->add('isFavourite', CheckboxType::class, [
                'label'    => 'favourite',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
            ])
            ->add('loveLevel', EnumType::class,  [
                'class' => Level::class,
                'required' => false,
                'expanded' => false,
                'multiple' => false,
                'autocomplete' => true,
            ])
            ->add('healthyLevel', EnumType::class,  [
                'class' => Level::class,
                'required' => false,
                'expanded' => false,
                'multiple' => false,
                'autocomplete' => true,
            ])
            ->add('localLevel', EnumType::class,  [
                'class' => Level::class,
                'required' => false,
                'expanded' => false,
                'multiple' => false,
                'autocomplete' => true,
            ])
            ->add('siteHighlights', EntityType::class, [
                'label' => 'Highlights',
                'class' => SiteHighlight::class,
                'required' => false,
                'query_builder' => function (EntityRepository $repo): QueryBuilder {
                    return $repo->createQueryBuilder('h')
                        ->orderBy('h.nameEn', 'ASC');
                },
                'multiple' => true,
                'autocomplete' => true,
                'by_reference' => false, // important for ManyToMany when using add/remove methods
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Food::class,
        ]);
    }
}
