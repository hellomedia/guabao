<?php

namespace Controlroom\Controller;

use App\Entity\Ingredient;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;

class IngredientCrudController extends AbstractCrudController
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {}

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
            // search fields implemented with custom strategy for normalizing accents
            // in createIndexQueryBuilder()
            ->setSearchFields([])
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

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $searchTerm = trim((string) $searchDto->getQuery());

        // No search: return
        if ($searchTerm == '') {
            return $qb;
        }

        // Search: Normalize search terms for accent-insensitive search
        // Normalize input exactly like SearchableName listener does
        $normalized = (string) $this->slugger->slug(mb_strtolower($searchTerm), ' ');

        $qb->andWhere('entity.nameSearch LIKE :q')
            ->setParameter('q', '%' . $normalized . '%');

        return $qb;
    }
}
