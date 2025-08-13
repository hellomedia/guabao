<?php

namespace Controlroom\Controller;

use App\Entity\Story;
use App\Entity\Trip;
use Doctrine\ORM\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class StoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Story::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Story')
            ->setEntityLabelInPlural('Stories')
            ->setDefaultSort([
                'trip' => 'ASC',
                'displayOrder' => 'ASC',
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
                    if ($trip->hasParent()) {
                        return $trip->getParent()->getShortNameWithFallback() . ' ' . $trip->getName();
                    }
                    return $trip->getName();
                }
            ]);
        
        yield NumberField::new('displayOrder');

        yield TextField::new('nameEn', 'Name EN');
        yield TextField::new('nameFr', 'Name FR');

        yield TextareaField::new('descriptionEn', 'Description EN');
        yield TextareaField::new('descriptionFr', 'Description FR');
    }
}
