<?php

namespace Controlroom\Form\Type;

use App\Entity\Ingredient;
use App\Enum\FoodType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngredientQuickEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nameEn')
            ->add('nameFr')
            ->add('foodType', EnumType::class,  [
                'class' => FoodType::class,
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
            'data_class' => Ingredient::class,
        ]);
    }
}
