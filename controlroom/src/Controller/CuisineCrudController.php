<?php

namespace Controlroom\Controller;

use App\Entity\Cuisine;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CuisineCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Cuisine::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Cuisine')
            ->setEntityLabelInPlural('Cuisines')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nameFr', 'Name FR');
        yield TextField::new('nameEn', 'Name EN');
    }
}
