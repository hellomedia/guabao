<?php

namespace Controlroom\Controller;

use App\Entity\Trip;
use App\Entity\TripHighlight;
use Doctrine\ORM\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TripHighlightCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TripHighlight::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Trip Highlight')
            ->setEntityLabelInPlural('Trip Highlights')
            ->setDefaultSort([
                'nameEn' => 'ASC'
            ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('trip')
            ->setFormTypeOptions([
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.startedAt', 'DESC'); // change to the field you want
                },
                'choice_label' => function (Trip $trip): string {
                    if ($trip->hasParentTrip()) {
                        return $trip->getParentTrip()->getShortNameWithFallback() . ' ' . $trip->getName();
                    }
                    return $trip->getName();
                }
            ]);
        
        yield TextField::new('nameEn', 'Name EN');
        yield TextField::new('nameFr', 'Name FR');

        yield TextareaField::new('descriptionEn', 'Description EN');
        yield TextareaField::new('descriptionFr', 'Description FR');
    }
}
