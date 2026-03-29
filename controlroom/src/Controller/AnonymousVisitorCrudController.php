<?php

namespace Controlroom\Controller;

use App\Entity\Stats\AnonymousVisitor;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Faker\Provider\Text;

class AnonymousVisitorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AnonymousVisitor::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Anonymous Visitor')
            ->setEntityLabelInPlural('Anonymous Visitors')

        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new('firstSeenAt'))
            ->add(DateTimeFilter::new('lastSeenAt'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield TextField::new('alias');
        yield TextField::new('visitorId');

        yield TextField::new('countryNamesAsString', 'Countries')
            ->onlyOnIndex()
            ->formatValue(static function ($value, $entity) {
                return $value ?: '—';
            });
        
        yield TextField::new('citiesAsString', 'Cities')
            ->onlyOnIndex()
            ->formatValue(static function ($value, $entity) {
                return $value ?: '—';
            });

        yield DateTimeField::new('firstSeenAt');
        yield DateTimeField::new('lastSeenAt');
        yield IntegerField::new('pageCount');
        yield AssociationField::new('visits')->hideOnForm();
        yield TextField::new('userAgent');
    }
}
