<?php

namespace Controlroom\Controller;

use App\Entity\Country;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CountryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Country::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Country')
            ->setEntityLabelInPlural('Countries')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('code');

        yield TextField::new('nameFr', 'Name FR');
        yield TextField::new('nameEn', 'Name EN');
    }
}
