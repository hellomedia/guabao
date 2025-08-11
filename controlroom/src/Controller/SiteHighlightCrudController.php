<?php

namespace Controlroom\Controller;

use App\Entity\SiteHighlight;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SiteHighlightCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SiteHighlight::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Site Highlight')
            ->setEntityLabelInPlural('Site Highlights')
            ->setDefaultSort([
                'nameEn' => 'ASC'
            ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nameEn', 'Name EN');
        yield TextField::new('nameFr', 'Name FR');

        yield TextareaField::new('descriptionEn', 'Description EN');
        yield TextareaField::new('descriptionFr', 'Description FR');
    }
}
