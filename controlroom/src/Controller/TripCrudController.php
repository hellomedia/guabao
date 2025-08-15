<?php

namespace Controlroom\Controller;

use App\Entity\Tag\TripTag;
use App\Entity\Trip;
use Doctrine\ORM\EntityRepository;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
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
    
        yield TextField::new('nameEn', 'Name EN');
        yield TextField::new('nameFr', 'Name FR');

        yield AssociationField::new('parent')
            ->hideOnIndex()
            ->setFormTypeOptions([
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->where('t.parent IS NULL')
                        ->orderBy('t.startedAt', 'DESC'); // change to the field you want
                },
                'choice_label' => function (Trip $trip): string {
                    return $trip->getPeriod() . ' ' . $trip->getName();
                }
            ]);
        yield AssociationField::new('children')
            ->setTemplatePath('@controlroom/field/trips.html.twig');

        yield AssociationField::new('stories')
            ->hideOnIndex()
            ->setTemplatePath('@controlroom/field/stories.html.twig');

        yield TextField::new('shortNameEn', 'Short EN');
        yield TextField::new('shortNameFr', 'Short FR');

        yield DateField::new('startedAt');
        yield DateField::new('endedAt');

        yield AssociationField::new('tags')
            ->setFormTypeOptions([
                'by_reference' => false, // important for ManyToMany when using add/remove methods
                'choice_label' => function (TripTag $tag) {
                    $locale = $this->getContext()?->getRequest()?->getLocale() ?? 'fr';
                    return $tag->getName($locale);
                }
            ])
            ->setTemplatePath('@controlroom/field/tags.html.twig');

        yield TextareaField::new('headlineEn', 'Headline EN')->hideOnIndex();
        yield TextareaField::new('headlineFr', 'Headline FR')->hideOnIndex();

        yield TextareaField::new('descriptionEn', 'Description EN')->hideOnIndex();
        yield TextareaField::new('descriptionFr', 'Description FR')->hideOnIndex();

        yield IntegerField::new('durationRating', 'Duration');
        yield IntegerField::new('adventureRating', 'Adventure');

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

        $editMedias = Action::new('editMedias', 'Edit Medias')
            ->linkToUrl(function (Trip $trip) {
                return $this->urlGenerator->generate('admin_trip_media_batch_edit', [
                    'id' => $trip->getId(),
                ]);
            })
            ->setIcon('fa fa-edit');

        $addMedias = Action::new('addMedias', '+ Add pictures')
            ->linkToUrl($this->urlGenerator->generate('admin_media_add_multiple'))
        ;

        $globalAddMedias = Action::new('addMedias', '+ Add pictures')
            ->linkToUrl($this->urlGenerator->generate('admin_media_add_multiple'))
            ->createAsGlobalAction()
        ;

        return $actions
            ->add(Action::DETAIL, $viewMedias)
            ->add(Action::DETAIL, $editMedias)
            ->add(Action::DETAIL, $addMedias)
            ->add(Action::INDEX, $globalAddMedias)
            ->add(Action::INDEX, $viewMedias)
            ->add(Action::INDEX, $editMedias)
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb->leftJoin('entity.countries', 'c')
            ->addSelect('c')
            ->leftJoin('entity.cover', 'cover')
            ->addSelect('cover')
            ->andWhere('entity.parent IS NULL')
        ;

        return $qb;
    }
}
