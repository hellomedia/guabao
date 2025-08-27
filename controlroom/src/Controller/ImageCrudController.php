<?php

namespace Controlroom\Controller;

use App\Entity\Tag\MediaTag;
use App\Entity\Tag\PlaceTag;
use App\Enum\MediaType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ImageCrudController extends MediaCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Image')
            ->setEntityLabelInPlural('Images')
            ->setDefaultSort(['takenAt' => 'DESC'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $addMutiple = Action::new('addMultiple', 'Add multiple')
            ->linkToUrl($this->urlGenerator->generate('admin_media_add_multiple'))
            ->setIcon('fa fa-images')
            ->createAsGlobalAction()
        ;

        return $actions
            ->add(Action::INDEX, $addMutiple)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addFieldset('Image');

        /**
         * Image fields
         * ============
         */

        // INDEX
        yield TextField::new('path', 'Image')
            ->setTemplatePath('@media/easyadmin/field/thumbnail.html.twig')
            ->onlyOnIndex();

        // DETAIL
        yield TextField::new('path', 'Thumbs')
            ->setTemplatePath('@media/easyadmin/field/media.html.twig')
            ->onlyOnDetail();
        
        // CREATE
        yield Field::new('imageFile')
            ->setFormType(FileType::class)
            ->setFormTypeOptions([
                'mapped' => false,
                'required' => false,
                'label' => 'New image',
            ])
            ->onlyWhenCreating();

        // UPDATE
        $entity = $this->getContext()?->getEntity()?->getInstance();
        $filename = $entity?->getFilename();

        yield Field::new('imageFile')
            ->setFormType(FileType::class)
            ->setFormTypeOptions([
                'mapped' => false,
                'required' => false,
                'label' => 'Replace image',
                'help' => 'Existing image: ' . $filename ?? ' - ',
            ])
            ->onlyWhenUpdating();
        
        /**
         * End Image fields
         * ================
         */

        yield DateTimeField::new('takenAt')
            ->setHelp('Leave empty for auto-fill from exif data');

        // TRIP
        yield FormField::addFieldset('Trip');
        yield AssociationField::new('trip')
            ->setHelp('Leave empty for auto-fill from exif data');

        yield BooleanField::new('isTripCover', 'Cover');
        yield BooleanField::new('isPano', 'Pano');
        yield BooleanField::new('is360', '360');

        // FOOD
        yield FormField::addFieldset('Food');
        yield AssociationField::new('food')
            ->setFormTypeOption('by_reference', false) // important for ManyToMany when using add/remove methods
            ->setTemplatePath('@controlroom/field/food_list.html.twig');
        
        yield BooleanField::new('isMeal');
        yield AssociationField::new('meal')
            ->hideOnIndex()
            ->setHelp('Leave empty, will be auto-filled if isMeal set to true');

        // PLACE
        yield FormField::addFieldset('Place');
        yield AssociationField::new('place');
        yield NumberField::new('latitude', 'lat')
            ->setNumDecimals(4)
            ->setFormTypeOption('scale', 7)
        ;
        yield NumberField::new('longitude', 'long')
            ->setNumDecimals(4)
            ->setFormTypeOption('scale', 7)
        ;
        yield TextField::new('googleMapsLink')
            ->setLabel('Maps')
            ->renderAsHtml()
            ->hideWhenCreating()
            ->hideWhenUpdating();

        // TEXT
        yield FormField::addFieldset('Text');
        yield TextareaField::new('descriptionFr', 'Desc FR')->hideOnIndex();
        yield TextareaField::new('descriptionEn', 'Desc EN')->hideOnIndex();

        // TAGS
        yield FormField::addFieldset('Tags');
        
        yield AssociationField::new('tags')
            ->setFormTypeOption('by_reference', false) // important for ManyToMany when using add/remove methods
            ->setTemplatePath('@controlroom/field/tags.html.twig')
        ;
        
        yield AssociationField::new('placeTags', 'Places')
            ->setFormTypeOption('by_reference', false) // important for ManyToMany when using add/remove methods
            ->setTemplatePath('@controlroom/field/tags.html.twig')
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb->leftJoin('entity.tags', 't')
            ->addSelect('t')
            ->leftJoin('entity.placeTags', 'pt')
            ->addSelect('pt')
            ->andWhere('entity.type = :video')
            ->setParameter('video', MediaType::IMAGE)
        ;

        return $qb;
    }
}
