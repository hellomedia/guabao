<?php

namespace Controlroom\Controller;

use App\Entity\Tag\TripTag;
use App\Entity\Trip;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TripCrudController extends AbstractCrudController
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    )
    {  
    }

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
        yield Field::new('cover.path', 'Cover')
            ->setTemplatePath('@media/easyadmin/field/thumbnail.html.twig')
            ->onlyOnIndex();

        yield Field::new('cover.path', 'Cover')
            ->setTemplatePath('@media/easyadmin/field/media.html.twig')
            ->onlyOnDetail();

        yield AssociationField::new('highlights')
            ->setTemplatePath('@media/easyadmin/field/thumbnail_list.html.twig')
            ->onlyOnDetail();
    
        yield AssociationField::new('tags')
            ->setFormTypeOptions([
                'by_reference' => false, // important for ManyToMany when using add/remove methods
                'choice_label' => function (TripTag $tag) {
                    $locale = $this->getContext()?->getRequest()?->getLocale() ?? 'fr';
                    return $tag->getName($locale);
                }
            ])
            ->setTemplatePath('@controlroom/field/tags.html.twig')
            ->setHelp('Hold Ctrl (or Cmd) to select multiple tags');
        
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

    public function configureActions(Actions $actions): Actions
    {
        $viewMedias = Action::new('viewMedias', 'Medias')
            ->linkToUrl(function (Trip $trip) {
                return $this->urlGenerator->generate('controlroom_media_index', [
                    'filters' => [
                        'trip' => [
                            'comparison' => '=',
                            'value' => $trip->getId(),
                        ]
                    ]
                ]);
            })
            ->setIcon('fa fa-images');

        return $actions
            ->add(Action::DETAIL, $viewMedias)
            ->add(Action::INDEX, $viewMedias);
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
