<?php

namespace Controlroom\Controller;

use App\Entity\Tag\MediaTag;
use App\Entity\Tag\PlaceTag;
use App\Enum\MediaType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class VideoCrudController extends MediaCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Video')
            ->setEntityLabelInPlural('Videos')
            ->setDefaultSort(['takenAt' => 'DESC'])
            ->setPaginatorPageSize(50)
        ;
    }

    public function configureFields(string $pageName): iterable
    {


        yield FormField::addFieldset('Video');

        yield TextField::new('vimeoId')
            ->setTemplatePath('@media/easyadmin/field/video_preview.html.twig')
            ->hideOnForm();

        yield TextField::new('takenAtHint')
            ->onlyOnForms()
            ->setHelp('String formatted as \'Ymd_His\' used to extract takenAt. eg: 20170816_190356');
        
        yield DateTimeField::new('takenAt')
            ->setHelp('Leave empty for auto-fill from exif data');
        
        yield TextField::new('vimeoId');

        yield ChoiceField::new('videoOrientation');

        // TRIP
        yield FormField::addFieldset('Trip');
        yield AssociationField::new('trip')
            ->setHelp('Leave empty for auto-fill from exif data');
        yield AssociationField::new('story');

        // PLACE
        yield FormField::addFieldset('Place');
        yield AssociationField::new('place');

        // TEXT
        yield FormField::addFieldset('Text');
        yield TextareaField::new('descriptionFr', 'Desc FR')->hideOnIndex();
        yield TextareaField::new('descriptionEn', 'Desc EN')->hideOnIndex();

        // TAGS
        yield FormField::addFieldset('Tags');
        
        yield AssociationField::new('tags')
            ->setFormTypeOptions([
                'by_reference' => false, // important for ManyToMany when using add/remove methods
                'choice_label' => function (MediaTag $tag) {
                    $locale = $this->getContext()?->getRequest()?->getLocale() ?? 'fr';
                    return $tag->getName($locale);
                }
            ])
            ->setTemplatePath('@controlroom/field/tags.html.twig')
        ;
        
        yield AssociationField::new('placeTags', 'Place tags')
            ->setFormTypeOptions([
                'by_reference' => false, // important for ManyToMany when using add/remove methods
                'choice_label' => function (PlaceTag $tag) {
                    $locale = $this->getContext()?->getRequest()?->getLocale() ?? 'fr';
                    return $tag->getName($locale);
                }
            ])
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
            ->setParameter('video', MediaType::VIDEO)
        ;

        return $qb;
    }
}
