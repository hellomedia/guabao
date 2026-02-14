<?php

namespace Controlroom\Controller;

use App\Entity\Ingredient;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class IngredientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ingredient::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Ingredient')
            ->setEntityLabelInPlural('Ingredients')
            ->setDefaultSort([
                'nameEn' => 'ASC'
            ])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $bulkEdit = Action::new('bulkEdit', 'Bulk edit')
            ->linkToRoute('admin_ingredient_bulk_edit')
            ->setIcon('fa fa-edit')
            ->createAsGlobalAction();

        $actions
            ->add(Action::INDEX, $bulkEdit)
        ;

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nameEn', 'Name EN');
        yield TextField::new('nameFr', 'Name FR');

        yield TextareaField::new('descriptionEn', 'Desc EN');
        yield TextareaField::new('descriptionFr', 'Desc FR');

        yield ChoiceField::new('foodType');

        yield AssociationField::new('similar')
            ->setFormTypeOption('by_reference', false) // important for ManyToMany when using add/remove methods
            ->setTemplatePath('@admin/field/ingredients.html.twig');

        yield AssociationField::new('food')
            ->setFormTypeOption('by_reference', false) // important for ManyToMany when using add/remove methods
            ->setTemplatePath('@admin/field/food_list.html.twig');

        yield BooleanField::new('favourite');
    }
}
