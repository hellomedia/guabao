<?php

namespace Controlroom\Controller;

use App\Entity\Meal;
use App\Entity\Tag\PlaceTag;
use App\Helper\GoogleMapsApiHelper;
use App\Helper\MealAutoFillHelper;
use Doctrine\ORM\EntityManagerInterface;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MealCrudController extends AbstractCrudController
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private MealAutoFillHelper $autoFillHelper,
        private GoogleMapsApiHelper $mapsApiHelper,
    ) {}

    public static function getEntityFqcn(): string
    {
        return Meal::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Meal')
            ->setEntityLabelInPlural('Meals')
            ->setDefaultSort([
                'enjoyedAt' => 'DESC'
            ])
            ->setSearchFields([
                'place.name',
            ])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('place'))
            ->add(EntityFilter::new('placeTags'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield AssociationField::new('medias')
            ->setTemplatePath('@media/easyadmin/field/thumbnail_list.html.twig')
            ->hideOnForm();

        yield TextField::new('name')->onlyOnIndex();

        yield DateTimeField::new('enjoyedAt');

        yield ChoiceField::new('type');

        yield AssociationField::new('place')
            ->setFormTypeOption('help', 'Leave null for autofill with existing place within 100m of meal medias')
        ;

        yield AssociationField::new('placeTags', 'Place tags')
            ->setTemplatePath('@controlroom/field/tags.html.twig')
            ->setFormTypeOption('by_reference', false) // important for ManyToMany when using add/remove methods
        ;

        yield AssociationField::new('siteHighlights', 'Highlights')
            ->setTemplatePath('@controlroom/field/highlights.html.twig')
            ->setFormTypeOption('by_reference', false) // important for ManyToMany when using add/remove methods
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewMedias = Action::new('viewMedias', 'Medias')
            ->linkToUrl(function (Meal $meal) {
                return $this->urlGenerator->generate('controlroom_media_index', [
                    'filters' => [
                        'meal' => [
                            'comparison' => '=',
                            'value' => $meal->getId(),
                        ]
                    ]
                ]);
            })
            ->setIcon('fa fa-images')
            ->addCssClass('btn btn-outline-primary');

        return $actions->add(Action::DETAIL, $viewMedias);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb->leftJoin('entity.medias', 'md')
            ->addSelect('md');

        return $qb;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->_customFormProcessing($entityInstance);

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->_customFormProcessing($entityInstance);

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function _customFormProcessing(Meal $meal): void
    {
        $this->autoFillHelper->autoAssignPlace($meal);

        if ($meal->getPlace() == null) {
            $suggestion = $this->autoFillHelper->suggestPlace($meal);

            if ($suggestion) {
                $this->addFlash('info', $suggestion);
            }
        }
    }
}
