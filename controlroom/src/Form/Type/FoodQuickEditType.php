<?php

namespace Controlroom\Form\Type;

use App\Entity\Cuisine;
use App\Entity\Food;
use App\Entity\Media;
use App\Entity\Tag\FoodTag;
use App\Enum\Level;
use Controlroom\Form\Field\IngredientAutocompleteField;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FoodQuickEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
            ])
            ->add('tags', EntityType::class, [
                'class' => FoodTag::class,
                'query_builder' => function (EntityRepository $repo): QueryBuilder {
                    return $repo->createQueryBuilder('t')
                        ->orderBy('t.nameEn', 'ASC');
                },
                'required' => false,
                'multiple' => true,
                'autocomplete' => true,
                'by_reference' => false, // important for ManyToMany when using add/remove methods
            ])
            ->add('cuisines', EntityType::class, [
                'class' => Cuisine::class,
                'query_builder' => function (EntityRepository $repo): QueryBuilder {
                    return $repo->createQueryBuilder('c')
                        ->orderBy('c.nameEn', 'ASC');
                },
                'required' => false,
                'multiple' => true,
                'autocomplete' => true,
                'by_reference' => false, // important for ManyToMany when using add/remove methods
            ])
            ->add('ingredients', IngredientAutocompleteField::class, [
                'required' => false,
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Food::class,
        ]);
    }
}
