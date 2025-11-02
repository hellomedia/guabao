<?php

namespace Controlroom\Controller;

use App\Entity\Cuisine;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
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
                'country' => 'ASC'
            ])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $bulkEdit = Action::new('bulkEdit', 'Bulk edit')
            ->linkToRoute('admin_cuisine_bulk_edit')
            ->setIcon('fa fa-edit')
            ->createAsGlobalAction();

        $actions
            ->add(Action::INDEX, $bulkEdit);

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('country');
        
        yield TextField::new('nameEn', 'Name EN');
        yield TextField::new('nameFr', 'Name FR');

        yield AssociationField::new('food')
            ->setFormTypeOption('by_reference', false) // important for ManyToMany when using add/remove methods
            ->setTemplatePath('@admin/field/food_list.html.twig');
    }
}
