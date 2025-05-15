<?php

namespace Controlroom\Controller;

use App\Entity\Food;
use App\Entity\Tag\FoodTag;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FoodCrudController extends AbstractCrudController
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public static function getEntityFqcn(): string
    {
        return Food::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Food')
            ->setEntityLabelInPlural('Food')
            ->setSearchFields([
                'name',
            ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield AssociationField::new('medias')
            ->setTemplatePath('@media/easyadmin/field/thumbnail_list.html.twig')
            ->hideOnForm();

        yield TextField::new('nameFr');
        yield TextField::new('nameEn');

        yield AssociationField::new('cuisine');

        yield AssociationField::new('tags')
            ->setFormTypeOptions([
                'by_reference' => false, // important for ManyToMany when using add/remove methods
                'choice_label' => function (FoodTag $tag) {
                    $locale = $this->getContext()?->getRequest()?->getLocale() ?? 'fr';
                    return $tag->getName($locale);
                }
            ])
            ->setTemplatePath('@controlroom/field/tags.html.twig')
            ->setHelp('Hold Ctrl (or Cmd) to select multiple tags');

        yield ChoiceField::new('loveLevel');
        yield ChoiceField::new('healthyLevel');
        yield ChoiceField::new('localLevel');
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewMedias = Action::new('viewMedias', 'Medias')
            ->linkToUrl(function (Food $food) {
                return $this->urlGenerator->generate('controlroom_media_index', [
                    'filters' => [
                        'food' => [
                            'comparison' => '=',
                            'value' => $food->getId(),
                        ]
                    ]
                ]);
            })
            ->setIcon('fa fa-images')
            ->addCssClass('btn btn-outline-primary');

        return $actions->add(Action::DETAIL, $viewMedias);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb->leftJoin('entity.medias', 'md')
            ->addSelect('md')
            ->leftJoin('entity.cuisine', 'c')
            ->addSelect('c')
            ->leftJoin('entity.tags', 't')
            ->addSelect('t');

        return $qb;
    }

}
