<?php

namespace Controlroom\Controller;

use App\Entity\Tag\Tag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tag::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Tag')
            ->setEntityLabelInPlural('Tags')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nameFr', 'Name (FR)');
        yield TextField::new('nameEn', 'Name (EN)');
    }
}
