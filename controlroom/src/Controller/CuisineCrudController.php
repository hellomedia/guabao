<?php

namespace Controlroom\Controller;

use App\Entity\Cuisine;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CuisineCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Cuisine::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Cuisine')
            ->setEntityLabelInPlural('Cuisines')
            ->setDefaultSort([
                'nameEn' => 'ASC'
            ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nameEn', 'Name EN');
        yield TextField::new('nameFr', 'Name FR');

        yield AssociationField::new('food')
            ->setFormTypeOption('by_reference', false) // important for ManyToMany when using add/remove methods
            ->setTemplatePath('@controlroom/field/food_list.html.twig');
    }
}
