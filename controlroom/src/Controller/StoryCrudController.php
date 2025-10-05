<?php

namespace Controlroom\Controller;

use App\Entity\Story;
use App\Entity\Tag\MediaTag;
use App\Entity\Trip;
use Doctrine\ORM\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StoryCrudController extends AbstractCrudController
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator)
    {
        
    }

    public static function getEntityFqcn(): string
    {
        return Story::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Story')
            ->setEntityLabelInPlural('Stories')
            ->setDefaultSort([
                'trip' => 'ASC',
                'displayOrder' => 'ASC',
            ])
            ->setSearchFields([
                'nameEn',
                'nameFr',
            ])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('trip'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewMedias = Action::new('viewMedias', 'Medias')
            ->linkToUrl(function (Story $story) {
                return $this->urlGenerator->generate('controlroom_media_index', [
                    'filters' => [
                        'story' => [
                            'comparison' => '=',
                            'value' => $story->getId(),
                        ]
                    ]
                ]);
            })
            ->setIcon('fa fa-images');

        $bulkEditMedias = Action::new('editMedias', 'Bulk edit')
            ->linkToUrl(function (Story $story) {
                return $this->urlGenerator->generate('admin_media_bulk_edit_by_story', [
                    'id' => $story->getId(),
                ]);
            })
            ->setIcon('fa fa-edit');

        $addImages = Action::new('addImages', '+ Bulk add images')
            ->linkToUrl(function (Story $story) {
                return $this->urlGenerator->generate('admin_media_bulk_add', [
                    'story' => $story->getId(),
                ]);
            })
        ;
    
        $globalAddImages = Action::new('addImages', '+ Bulk add images')
            ->linkToUrl($this->urlGenerator->generate('admin_media_bulk_add'))
            ->createAsGlobalAction();

        return $actions
            ->add(Action::DETAIL, $viewMedias)
            ->add(Action::DETAIL, $bulkEditMedias)
            ->add(Action::DETAIL, $addImages)
            ->add(Action::INDEX, $globalAddImages)
            ->add(Action::INDEX, $viewMedias)
            ->add(Action::INDEX, $bulkEditMedias)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('trip')
            ->setFormTypeOptions([
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.startedAt', 'DESC');
                },
                'choice_label' => function (Trip $trip): string {
                    if ($trip->hasParent()) {
                        return $trip->getParent()->getShortNameWithFallback() . ' ' . $trip->getName();
                    }
                    return $trip->getName();
                }
            ]);

        yield NumberField::new('displayOrder');

        yield TextField::new('nameEn', 'Name EN');
        yield TextField::new('nameFr', 'Name FR');

        yield BooleanField::new('showTitle')
            ->renderAsSwitch(true);

        yield AssociationField::new('tags')
            ->setFormTypeOption('by_reference', false) // important for ManyToMany when using add/remove methods
            ->setTemplatePath('@controlroom/field/tags.html.twig');

        yield AssociationField::new('placeTags')
            ->setFormTypeOption('by_reference', false) // important for ManyToMany when using add/remove methods
            ->setTemplatePath('@controlroom/field/tags.html.twig');

        yield TextareaField::new('descriptionEn', 'Description EN');
        yield TextareaField::new('descriptionFr', 'Description FR');
    }
}
