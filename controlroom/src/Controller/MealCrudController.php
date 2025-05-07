<?php

namespace Controlroom\Controller;

use App\Entity\Food;
use App\Entity\Meal;
use App\Entity\Tag\FoodTag;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MealCrudController extends AbstractCrudController
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
            ->setEntityLabelInSingular('Meal')
            ->setEntityLabelInPlural('Meals')
            ->setSearchFields([
                'name',
            ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield DateField::new('enjoyedAt');
        
        yield AssociationField::new('pictures')
            ->setTemplatePath('@controlroom/field/picture_collection_thumbnails.html.twig')
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewPictures = Action::new('viewPictures', 'Pictures')
            ->linkToUrl(function (Meal $meal) {
                return $this->urlGenerator->generate('controlroom_picture_index', [
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

        return $actions->add(Action::DETAIL, $viewPictures);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb->leftJoin('entity.pictures', 'p')
            ->addSelect('p');

        return $qb;
    }

}
