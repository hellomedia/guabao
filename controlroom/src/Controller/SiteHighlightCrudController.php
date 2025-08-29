<?php

namespace Controlroom\Controller;

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
            ->setTemplatePath('@controlroom/field/trips.html.twig')
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
            ->setTemplatePath('@controlroom/field/stories.html.twig')
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

        yield TextareaField::new('descriptionEn', 'Description EN');
        yield TextareaField::new('descriptionFr', 'Description FR');
    }
}
