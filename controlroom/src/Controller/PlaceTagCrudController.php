<?php

namespace Controlroom\Controller;

use App\Entity\Tag\PlaceTag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
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
            ->setDefaultSort([
                'country' => 'ASC',
                'nameEn' => 'ASC'
            ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nameEn', 'Name EN');
        yield TextField::new('nameFr', 'Name FR');

        yield AssociationField::new('country');

        yield TextareaField::new('descriptionEn', 'Description EN');
        yield TextareaField::new('descriptionFr', 'Description FR');
    }
}
