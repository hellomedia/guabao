<?php

namespace Controlroom\Controller;

use App\Entity\Chain;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ChainCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Chain::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Chain')
            ->setEntityLabelInPlural('Chains')
            ->setDefaultSort([
                'nameEn' => 'ASC'
            ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nameEn');
        yield TextField::new('nameFr');

        yield TextField::new('originalName');

        yield BooleanField::new('favourite');
                    
        yield TextareaField::new('descriptionEn', 'Description EN');
        yield TextareaField::new('descriptionFr', 'Description FR');
    }
}