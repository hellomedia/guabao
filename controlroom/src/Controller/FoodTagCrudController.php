<?php

namespace Controlroom\Controller;

use App\Entity\Tag\FoodTag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FoodTagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FoodTag::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Food Tag')
            ->setEntityLabelInPlural('Food Tags')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nameFr', 'Name (FR)');
        yield TextField::new('nameEn', 'Name (EN)');
    }
}
