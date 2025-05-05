<?php

namespace Controlroom\Controller;

use App\Entity\Trip;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TripCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Trip::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Trip')
            ->setEntityLabelInPlural('Trips')
            ->setDefaultSort(['startedAt' => 'DESC'])
        ;
    }


    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nameFr', 'Name (FR)');
        yield TextField::new('nameEn', 'Name (EN)');

        yield DateField::new('startedAt');
        yield DateField::new('endedAt');

        yield AssociationField::new('countries')
            ->setTemplatePath('@controlroom/field/countries.html.twig');
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb->leftJoin('entity.countries', 'c')
            ->addSelect('c');

        return $qb;
    }
}
