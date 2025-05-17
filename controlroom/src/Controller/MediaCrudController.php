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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
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

class MediaCrudController extends AbstractCrudController
{
    public function __construct(
        private MediaAutoFillHelper $autoFillHelper,
        private GoogleMapsApiHelper $mapsApiHelper,
        private ExifExtractor $exifExtractor,
        private UploadHelper $uploadHelper,
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
            ->setDefaultSort(['takenAt' => 'ASC'])
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

        yield FormField::addFieldset('Trip');
        yield AssociationField::new('trip')
            ->setHelp('Leave empty for auto-fill from exif data');

        yield BooleanField::new('highlight');
        yield AssociationField::new('highlightedTrip')->onlyOnDetail(); // !! not on forms -- buggy because overrides logic in setHighlight()
        yield BooleanField::new('isPano', 'Pano');
        yield BooleanField::new('is360', '360');
        yield BooleanField::new('isTripCover', 'Cover');

        yield FormField::addFieldset('Food');
        yield AssociationField::new('food');
        yield BooleanField::new('isMeal');
        yield AssociationField::new('meal')
            ->setHelp('Leave empty, will be auto-filled if isMeal set to true');

        yield FormField::addFieldset('Place');
        yield AssociationField::new('place');
        yield NumberField::new('latitude', 'lat')->setFormTypeOption('scale', 7);
        yield NumberField::new('longitude', 'long')->setFormTypeOption('scale', 7);
        yield TextField::new('googleMapsLink')
            ->setLabel('Maps')
            ->renderAsHtml()
            ->hideWhenCreating()
            ->hideWhenUpdating();

        yield FormField::addFieldset('Text');
        yield TextareaField::new('descriptionFr', 'Desc FR');
        yield TextareaField::new('descriptionEn', 'Desc EN');

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
            ->setHelp('Hold Ctrl (or Cmd) to select multiple tags');
        
        yield AssociationField::new('placeTags', 'Places')
            ->setFormTypeOptions([
                'by_reference' => false, // important for ManyToMany when using add/remove methods
                'choice_label' => function (PlaceTag $tag) {
                    $locale = $this->getContext()?->getRequest()?->getLocale() ?? 'fr';
                    return $tag->getName($locale);
                }
            ])
            ->setTemplatePath('@controlroom/field/tags.html.twig')
            ->setHelp('Hold Ctrl (or Cmd) to select multiple tags');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('trip'))
            ->add(EntityFilter::new('food'))
            ->add(EntityFilter::new('meal'))
        ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->_customFormProcessing($entityManager, $entityInstance);

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->_customFormProcessing($entityManager, $entityInstance);

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function _customFormProcessing(EntityManagerInterface $entityManager, Media $media): void 
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

            $this->_updateAutoFields($media, $entityManager, $exif);
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

        if ($image->isHighlight()) {
            return false;
        }

        if ($image->isTripCover()) {
            return false;
        }

        return true;
    }

    private function _updateAutoFields(Media $media, EntityManagerInterface $entityManager, array|false $exif)
    {
        $this->autoFillHelper->_setTakenAt($media, $exif);

        $this->autoFillHelper->_setCoordinates($media, $exif);

        $this->autoFillHelper->_autoAssignPlace($media, $entityManager);

        if ($media->getPlace() == null) {
            $this->_suggestPlace($media);
        }

        $this->autoFillHelper->_setTrip($media);

        $this->autoFillHelper->_setMeal($media);
    }

    private function _suggestPlace(Media $media)
    {
        $lat = $media->getLatitude();
        $lng = $media->getLongitude();

        // No nearby match, suggest new place
        // Suggestion only — don't persist
        $suggestion = $this->mapsApiHelper->findNearbyPlace($lat, $lng);

        if ($suggestion !== null) {
            $name = $suggestion['name'] ?? 'Unknown';
            $address = $suggestion['vicinity'] ?? 'Unknown address';
            $placeId = $suggestion['place_id'] ?? null;
            $location = $suggestion['geometry']['location'] ?? [];

            $placeUrl = $placeId
                ? 'https://www.google.com/maps/place/?q=place_id=' . urlencode($placeId)
                : null;

            $coordUrl = isset($location['lat'], $location['lng'])
                ? sprintf('https://www.google.com/maps/search/?api=1&query=%f,%f', $location['lat'], $location['lng'])
                : null;

            $linkParts = [];
            if ($placeUrl) {
                $linkParts[] = sprintf('<a href="%s" target="_blank">Google Maps (place)</a>', $placeUrl);
            }
            if ($coordUrl) {
                $linkParts[] = sprintf('<a href="%s" target="_blank">By coordinates</a>', $coordUrl);
            }

            $this->addFlash('info', sprintf(
                '📍 Suggested place: <strong>%s</strong><br><small>%s</small><br>%s',
                htmlspecialchars($name),
                htmlspecialchars($address),
                implode(' | ', $linkParts)
            ));
        }
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
