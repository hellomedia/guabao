<?php

namespace Controlroom\Controller;

use App\Entity\Stats\AnonymousPageView;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AnonymousPageViewCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AnonymousPageView::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setSearchFields([
                'visit.id',
                'visit.visitor.id',
                'visit.visitor.alias',
            ])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('visit')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield AssociationField::new('visit');
        yield DateTimeField::new('visitedAt');
        yield TextField::new('path');
        yield TextField::new('routeName');
    }
}
