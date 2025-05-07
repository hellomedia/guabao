<?php

namespace Controlroom\Controller;

use App\Entity\Trip;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TripCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Trip::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Trip')
            ->setEntityLabelInPlural('Trips')
            ->setDefaultSort(['startedAt' => 'DESC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field::new('cover.imageFile', 'Cover')
            ->setTemplatePath('@controlroom/field/picture_thumbnail.html.twig')
            ->onlyOnIndex();

        yield Field::new('cover.imageFile', 'Cover')
            ->setTemplatePath('@controlroom/field/picture.html.twig')
            ->onlyOnDetail();

        yield AssociationField::new('highlights')
            ->setTemplatePath('@controlroom/field/picture_collection_thumbnails.html.twig')
            ->onlyOnDetail();
    
        yield ChoiceField::new('type');
        
        yield TextField::new('nameFr', 'Name FR');
        yield TextField::new('nameEn', 'Name EN');

        yield DateField::new('startedAt');
        yield DateField::new('endedAt');

        yield TextareaField::new('headlineFr', 'Headline FR');
        yield TextareaField::new('headlineEn', 'Headline EN');

        yield TextareaField::new('descriptionFr', 'Description FR')->hideOnIndex();
        yield TextareaField::new('descriptionEn', 'Description EN')->hideOnIndex();

        yield AssociationField::new('countries')
            ->setTemplatePath('@controlroom/field/countries.html.twig');
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb->leftJoin('entity.countries', 'c')
            ->addSelect('c')
            ->leftJoin('entity.cover', 'cover')
            ->addSelect('cover')
        ;

        return $qb;
    }
}
