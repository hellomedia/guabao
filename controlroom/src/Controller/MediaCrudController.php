<?php

namespace Controlroom\Controller;

use App\Entity\Media;
use App\Entity\Tag\MediaTag;
use App\Entity\Tag\PlaceTag;
use App\Enum\MediaType;
use App\Helper\GoogleMapsApiHelper;
use App\Helper\MediaAutoFillHelper;
use App\Pack\Media\Helper\ExifExtractor;
use App\Pack\Media\Helper\UploadHelper;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
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
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MediaCrudController extends AbstractCrudController
{
    public function __construct(
        private MediaAutoFillHelper $autoFillHelper,
        private GoogleMapsApiHelper $mapsApiHelper,
        private ExifExtractor $exifExtractor,
        private UploadHelper $uploadHelper,
        private UrlGeneratorInterface $urlGenerator,
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Media')
            ->setEntityLabelInPlural('Medias')
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

        // VIDEO
        yield FormField::addFieldset('Video');

        yield TextField::new('takenAtHint')
            ->onlyOnForms()
            ->setHelp('String formatted as \'Ymd_His\' used to extract takenAt. eg: 20170816_190356');
        
        yield TextField::new('vimeoId')
                ->onlyOnForms();

        // SHARED
        yield FormField::addFieldset('Shared');

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
        yield AssociationField::new('food');
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
            ->setFormTypeOptions([
                'by_reference' => false, // important for ManyToMany when using add/remove methods
                'choice_label' => function (MediaTag $tag) {
                    $locale = $this->getContext()?->getRequest()?->getLocale() ?? 'fr';
                    return $tag->getName($locale);
                }
            ])
            ->setTemplatePath('@controlroom/field/tags.html.twig')
        ;
        
        yield AssociationField::new('placeTags', 'Places')
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

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('trip'))
            ->add(EntityFilter::new('story'))
        ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->_customFormProcessing($entityInstance);

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->_customFormProcessing($entityInstance);

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function _customFormProcessing(Media $media): void 
    {
        // 'Media' = entity name
        // 'imageFile' = name of field with FileType form type
        $uploadedFile = $this->getContext()->getRequest()->files->get('Media')['imageFile'] ?? null;

        if ($uploadedFile instanceof UploadedFile) {

            $media->setType(MediaType::IMAGE);

            // extract exif before upload -- exif data lost during conversion from jpeg to avif
            $exif = $this->exifExtractor->extractExifData($uploadedFile);

            // upload and convert to avif
            $this->uploadHelper->uploadImage($media, $uploadedFile, resize: $this->_shouldBeResized($media));
        }

        $this->_updateAutoFields($media, $exif ?? false);

        // Video from Vimeo
        if ($media->getVimeoId() != null) {

            $media->setType(MediaType::VIDEO);
            
            // set takenAt from hint
            if ($media->getTakenAtHint() != null) {
                $takenAt = DateTimeImmutable::createFromFormat('Ymd_His', $media->getTakenAtHint());
                $media->setTakenAt($takenAt);
                $media->setTakenAtHint(null);
            }
            
            $this->autoFillHelper->autoAssignTrip($media);
        }
    }

    private function _shouldBeResized(Media $image): bool
    {
        if ($image->is360()) {
            return false;
        }

        if ($image->isPano()) {
            return false;
        }

        if ($image->isTripCover()) {
            return false;
        }

        return true;
    }

    private function _updateAutoFields(Media $media, array|false $exif)
    {
        if ($exif) {
            $this->autoFillHelper->setTakenAt($media, $exif);
            $this->autoFillHelper->setCoordinates($media, $exif);
        }

        $this->autoFillHelper->autoAssignPlace($media);

        if ($media->getPlace() == null) {
            $suggestion = $this->autoFillHelper->suggestPlace($media);

            if ($suggestion) {
                $this->addFlash('info', $suggestion);
            }
        }

        $this->autoFillHelper->autoAssignTrip($media);
        $this->autoFillHelper->autoAssignMeal($media);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb->leftJoin('entity.tags', 't')
            ->addSelect('t')
            ->leftJoin('entity.placeTags', 'pt')
            ->addSelect('pt')
        ;

        return $qb;
    }
}
