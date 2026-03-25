<?php

namespace Controlroom\Controller;

use App\Entity\Stats\AnonymousVisit;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class AnonymousVisitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AnonymousVisit::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Anonymous Visit')
            ->setEntityLabelInPlural('Anonymous Visits')

        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('alias'))
            ->add(TextFilter::new('visitorId'))
            ->add(TextFilter::new('ip'))
            ->add(TextFilter::new('countryCode'))
            ->add(TextFilter::new('cityName'))
            ->add(DateTimeFilter::new('lastSeenAt'))
            ->add(BooleanFilter::new('isReturning'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield TextField::new('sessionId');
        yield TextField::new('visitorId');
        yield TextField::new('alias');
        yield TextField::new('ip');
        yield TextField::new('countryCode', 'Country');
        yield TextField::new('cityName', 'City');
        yield DateTimeField::new('startedAt');
        yield DateTimeField::new('lastSeenAt');
        yield IntegerField::new('pageCount');
        yield BooleanField::new('isReturning');
    }
}
