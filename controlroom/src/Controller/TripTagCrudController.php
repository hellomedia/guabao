<?php

namespace Controlroom\Controller;

use App\Entity\Tag\TripTag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
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
            ->setDefaultSort([
                'nameEn' => 'ASC'
            ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('key');
        
        yield TextField::new('nameEn', 'Name EN');
        yield TextField::new('nameFr', 'Name FR');

        yield TextField::new('slugEn', 'Slug EN')->hideOnForm();
        yield TextField::new('slugFr', 'Slug FR')->hideOnForm();

        yield TextareaField::new('descriptionEn', 'Description EN');
        yield TextareaField::new('descriptionFr', 'Description FR');
    }
}
