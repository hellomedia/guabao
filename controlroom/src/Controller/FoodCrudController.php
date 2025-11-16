<?php

namespace Controlroom\Controller;

use App\Entity\Food;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FoodCrudController extends AbstractCrudController
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public static function getEntityFqcn(): string
    {
        return Food::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Food')
            ->setEntityLabelInPlural('Food')
            ->setDefaultSort([
                'cuisines' => 'ASC',
                'nameEn' => 'ASC'
            ])
            ->setSearchFields([
                'nameEn',
                'nameFr'
            ])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('cuisines'))
            ->add(EntityFilter::new('tags'))
            ->add(EntityFilter::new('parent'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnDetail();

        yield AssociationField::new('medias')
            ->setTemplatePath('@media/easyadmin/field/thumbnail_list.html.twig')
            ->hideOnForm();
            
        yield TextField::new('nameEn');
        yield TextField::new('nameFr');

        yield AssociationField::new('parent')
            ->setFormTypeOptions([
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('f')
                    ->where('f.parent IS NULL')
                    ->orderBy('f.nameEn', 'ASC'); // change to the field you want
            },
        ]);

        yield AssociationField::new('children')
            ->setTemplatePath('@admin/field/food_list.html.twig');

        yield TextareaField::new('descriptionEn');
        yield TextareaField::new('descriptionFr')->hideOnIndex();

        yield AssociationField::new('cuisines')
            ->setFormTypeOption('by_reference', false) // important for ManyToMany when using add/remove methods
            ->setTemplatePath('@admin/field/cuisines.html.twig');

        yield AssociationField::new('tags')
            ->setFormTypeOption('by_reference', false) // important for ManyToMany when using add/remove methods
            ->setTemplatePath('@admin/field/tags.html.twig')
        ;

        yield AssociationField::new('ingredients')
            ->setFormTypeOption('by_reference', false) // important for ManyToMany when using add/remove methods
            ->setTemplatePath('@admin/field/ingredients.html.twig');

        yield ChoiceField::new('loveLevel',' Love');
        yield ChoiceField::new('healthyLevel', 'Healthy');
        yield ChoiceField::new('localLevel', 'Local');

    }

    public function configureActions(Actions $actions): Actions
    {
        $viewMedias = Action::new('viewMedias', 'Medias')
            ->linkToUrl(function (Food $food) {
                return $this->urlGenerator->generate('controlroom_media_index', [
                    'filters' => [
                        'food' => [
                            'comparison' => '=',
                            'value' => $food->getId(),
                        ]
                    ]
                ]);
            })
            ->setIcon('fa fa-images')
            ->addCssClass('btn btn-outline-primary');

        $bulkEditMedias = Action::new('editMedias', 'Bulk edit')
            ->linkToUrl(function (Food $food) {
                return $this->urlGenerator->generate('admin_media_bulk_edit_by_food', [
                    'id' => $food->getId(),
                ]);
            })
            ->setIcon('fa fa-edit');

        $actions
            ->add(Action::DETAIL, $viewMedias)
            ->add(Action::DETAIL, $bulkEditMedias)
            ->add(Action::INDEX, $bulkEditMedias)
        ;

        return $actions;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb->leftJoin('entity.medias', 'md')
            ->addSelect('md')
            ->leftJoin('entity.cuisines', 'c')
            ->addSelect('c')
            ->leftJoin('entity.tags', 't')
            ->addSelect('t');

        return $qb;
    }

}
