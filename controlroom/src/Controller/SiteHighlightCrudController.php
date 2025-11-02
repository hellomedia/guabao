<?php

namespace Controlroom\Controller;

use App\Entity\Meal;
use App\Entity\Place;
use App\Entity\SiteHighlight;
use App\Entity\Story;
use App\Entity\Trip;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
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

        yield AssociationField::new('medias')
            ->setTemplatePath('@media/easyadmin/field/thumbnail_list.html.twig')
            ->hideOnForm();

        yield AssociationField::new('trips')
            ->setTemplatePath('@admin/field/trips.html.twig')
            ->setFormTypeOption('query_builder', function (EntityRepository $repo): QueryBuilder {
                return $repo->createQueryBuilder('t')
                    ->orderBy('t.startedAt', 'DESC');
            })
            ->setFormTypeOption('group_by', function (Trip $trip, $key, $value) {
                if ($trip->hasParent()) {
                    return $trip->getParent()->getNameEn();
                }
                return $trip->getNameEn();
            })
        ;

        yield AssociationField::new('stories')
            ->setTemplatePath('@admin/field/stories.html.twig')
            ->setFormTypeOption('query_builder', function (EntityRepository $repo): QueryBuilder {
                return $repo->createQueryBuilder('s')
                    ->join('s.trip', 't')
                    ->addOrderBy('t.startedAt', 'DESC')
                    ->addOrderBy('s.displayOrder', 'ASC');
            })
            ->setFormTypeOption('group_by', function (Story $story, $key, $value) {
                return $story->getTrip()->getNameEn();
            })
        ;
    
        yield AssociationField::new('places')
            ->setTemplatePath('@admin/field/places.html.twig')
            ->setFormTypeOption('query_builder', function (EntityRepository $repo): QueryBuilder {
                return $repo->createQueryBuilder('p')
                    ->join('p.placeTags', 'pt')
                    ->join('p.country', 'c')
                    ->addOrderBy('c.nameEn', 'ASC')
                    ->addOrderBy('pt.nameEn', 'ASC')
                    ->addOrderBy('p.name', 'ASC');
            })
            ->setFormTypeOption('group_by', function (Place $place, $key, $value) {
                return $place->getPlaceTags()->first()->getNameEn();
            })
        ;

        yield AssociationField::new('meals')
            ->setTemplatePath('@admin/field/meals.html.twig')
            ->setFormTypeOption('query_builder', function (EntityRepository $repo): QueryBuilder {
                return $repo->createQueryBuilder('m')
                    ->join('m.placeTags', 'pt')
                    ->join('pt.country', 'c')
                    ->addOrderBy('c.nameEn', 'ASC')
                    ->addOrderBy('pt.nameEn', 'ASC')
                    ->addOrderBy('m.enjoyedAt', 'DESC');
            })
            ->setFormTypeOption('group_by', function (Meal $meal, $key, $value) {
                return $meal->getPlaceTags()->first()->getNameEn();
            })
        ;

        yield TextareaField::new('descriptionEn', 'Description EN');
        yield TextareaField::new('descriptionFr', 'Description FR');
    }
}
