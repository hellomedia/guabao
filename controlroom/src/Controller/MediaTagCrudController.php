<?php

namespace Controlroom\Controller;

use App\Entity\Tag\MediaTag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MediaTagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MediaTag::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Media Tag')
            ->setEntityLabelInPlural('Media Tags')
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
