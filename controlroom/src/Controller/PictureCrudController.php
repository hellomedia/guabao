<?php

namespace Controlroom\Controller;

use App\Entity\Picture;
use App\Entity\Tag\PlaceTag;
use App\Entity\Tag\Tag;
use App\Helper\GoogleMapsApiHelper;
use App\Helper\PictureAutoFillHelper;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PictureCrudController extends AbstractCrudController
{
    public function __construct(
        private PictureAutoFillHelper $autoFillHelper,
        private GoogleMapsApiHelper $mapsApiHelper,
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return Picture::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Picture')
            ->setEntityLabelInPlural('Pictures')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addFieldset('Photo');
        
        yield Field::new('imageFile')
            ->setTemplatePath('@controlroom/field/picture_thumbnail.html.twig')
            ->onlyOnIndex();

        yield Field::new('imageFile')
            ->setTemplatePath('@controlroom/field/picture.html.twig')
            ->onlyOnDetail();

        yield TextField::new('imageFile')
            ->setFormType(VichImageType::class)
            ->setLabel('Upload image')
            ->onlyOnForms();

        yield DateTimeField::new('takenAt')
            ->setHelp('Leave empty for auto-fill from exif data');

        yield FormField::addFieldset('Food');
        yield AssociationField::new('food');
        yield BooleanField::new('isMeal');
        yield AssociationField::new('meal')
            ->setHelp('Leave empty, will be auto-filled if isMeal set to true');

        yield FormField::addFieldset('Trip');
        yield AssociationField::new('trip')
            ->setHelp('Leave empty for auto-fill from exif data');
        yield BooleanField::new('highlight');

        yield FormField::addFieldset('Place');
        yield AssociationField::new('place');
        yield NumberField::new('latitude')->setFormTypeOption('scale', 7);
        yield NumberField::new('longitude')->setFormTypeOption('scale', 7);
        yield TextField::new('googleMapsLink')
            ->setLabel('Google Maps')
            ->renderAsHtml()
            ->hideWhenCreating()
            ->hideWhenUpdating();

        yield FormField::addFieldset('Text');
        yield TextField::new('descriptionFr');
        yield TextField::new('descriptionEn');

        yield FormField::addFieldset('Tags');
        
        yield AssociationField::new('tags')
            ->setFormTypeOptions([
                'by_reference' => false, // important for ManyToMany when using add/remove methods
                'choice_label' => function (Tag $tag) {
                    $locale = $this->getContext()?->getRequest()?->getLocale() ?? 'fr';
                    return $tag->getName($locale);
                }
            ])
            ->setTemplatePath('@controlroom/field/tags.html.twig')
            ->setHelp('Hold Ctrl (or Cmd) to select multiple tags');
        
        yield AssociationField::new('placeTags')
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
        $this->_updateAutoFields($entityInstance, $entityManager);

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {          
        $this->_updateAutoFields($entityInstance, $entityManager);

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function _updateAutoFields(Picture $picture, $entityManager)
    {
        $exif = $this->autoFillHelper->_extractExifData($picture);

        $this->autoFillHelper->_setTakenAt($picture, $exif);

        $this->autoFillHelper->_setCoordinates($picture, $exif);

        $this->autoFillHelper->_autoAssignPlace($picture, $entityManager);

        if ($picture->getPlace() == null) {
            $this->_suggestPlace($picture);
        }

        $this->autoFillHelper->_setTrip($picture);

        $this->autoFillHelper->_setMeal($picture);
    }

    private function _suggestPlace(Picture $picture)
    {
        $lat = $picture->getLatitude();
        $lng = $picture->getLongitude();

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
