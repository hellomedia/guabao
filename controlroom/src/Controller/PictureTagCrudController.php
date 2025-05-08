<?php

namespace Controlroom\Controller;

use App\Entity\Tag\PictureTag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PictureTagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PictureTag::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Picture Tag')
            ->setEntityLabelInPlural('Picture Tags')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nameFr', 'Name FR');
        yield TextField::new('nameEn', 'Name EN');

        yield TextField::new('descriptionFr', 'Description FR');
        yield TextField::new('descriptionEn', 'Description EN');
    }
}
