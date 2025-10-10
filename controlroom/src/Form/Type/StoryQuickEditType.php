<?php

namespace Controlroom\Form\Type;

use App\Entity\SiteHighlight;
use App\Entity\Story;
use App\Entity\Tag\MediaTag;
use App\Entity\Tag\PlaceTag;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoryQuickEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $story = $builder->getData();

        $builder
            ->add('nameEn', TextType::class, [
                'label' => 'Name EN',
            ])
            ->add('descriptionEn', TextareaType::class, [
                'label' => "Text above gallery EN",
                'required' => false,
                'attr' => [
                    'rows' => 3,
                ],
             ])
            ->add('textBelowGalleryEn', TextareaType::class, [
                'label' => "Text below gallery EN",
                'required' => false,
                'attr' => [
                    'rows' => 3,
                ],
            ])
            ->add('nameFr', TextType::class, [
                'label' => 'Name FR',
            ])
            ->add('descriptionFr', TextareaType::class, [
                'label' => "Text above gallery FR",
                'required' => false,
                'attr' => [
                    'rows' => 3,
                ],
            ])
            ->add('textBelowGalleryFr', TextareaType::class, [
                'label' => "Text below gallery FR",
                'required' => false,
                'attr' => [
                    'rows' => 3,
                ],
            ])
            ->add('showTitle', CheckboxType::class, [
                'label'    => 'show title',
                'required' => false,
                'row_attr' => ['class' => 'form-switch'], // for switch
                'attr'     => ['class' => 'form-check-input'], // for switch
            ])
            ->add('tags', EntityType::class, [
                'class' => MediaTag::class,
                'query_builder' => function (EntityRepository $repo): QueryBuilder {
                    return $repo->createQueryBuilder('t')
                        ->orderBy('t.nameEn', 'ASC');
                },
                'required' => false,
                'multiple' => true,
                'autocomplete' => true,
                'by_reference' => false, // important for ManyToMany when using add/remove methods
            ])
            ->add('placeTags', EntityType::class, [
                'class' => PlaceTag::class,
                'query_builder' => function (EntityRepository $repo) use ($story): QueryBuilder {
                    return $repo->createQueryBuilder('pt')
                        ->where('pt.country IN (:countries)')
                        ->setParameter('countries', $story->getTrip()->getCountries())
                        ->orderBy('pt.nameEn', 'ASC');
                },
                'required' => false,
                'multiple' => true,
                'autocomplete' => true,
                'by_reference' => false, // important for ManyToMany when using add/remove methods
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
            'data_class' => Story::class,
        ]);
    }
}
