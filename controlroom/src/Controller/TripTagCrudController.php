<?php

namespace Controlroom\Controller;

use App\Entity\Tag\TripTag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TripTagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TripTag::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Trip Tag')
            ->setEntityLabelInPlural('Trip Tags')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('key');
        
        yield TextField::new('nameFr', 'Name FR');
        yield TextField::new('nameEn', 'Name EN');

        yield TextField::new('slugFr', 'Slug FR')->hideOnForm();
        yield TextField::new('slugEn', 'Slug EN')->hideOnForm();

        yield TextField::new('descriptionFr', 'Description FR');
        yield TextField::new('descriptionEn', 'Description EN');
    }
}
