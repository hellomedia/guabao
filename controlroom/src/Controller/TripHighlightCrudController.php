<?php

namespace Controlroom\Controller;

use App\Entity\TripHighlight;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TripHighlightCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TripHighlight::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Trip Highlight')
            ->setEntityLabelInPlural('Trip Highlights')
            ->setDefaultSort([
                'nameEn' => 'ASC'
            ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('trip');
        
        yield TextField::new('nameEn', 'Name EN');
        yield TextField::new('nameFr', 'Name FR');

        yield TextareaField::new('descriptionEn', 'Description EN');
        yield TextareaField::new('descriptionFr', 'Description FR');
    }
}
