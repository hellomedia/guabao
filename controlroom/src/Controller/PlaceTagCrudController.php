<?php

namespace Controlroom\Controller;

use App\Entity\Tag\PlaceTag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PlaceTagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PlaceTag::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Place Tag')
            ->setEntityLabelInPlural('Place Tags')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nameFr', 'Name (FR)');
        yield TextField::new('nameEn', 'Name (EN)');

        yield AssociationField::new('country');

        yield TextField::new('descriptionFr', 'Description (FR)');
        yield TextField::new('descriptionEn', 'Description (EN)');
    }
}
