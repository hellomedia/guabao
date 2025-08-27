<?php

namespace Controlroom\Controller;

use App\Entity\Place;
use App\Entity\Tag\PlaceTag;
use App\Helper\PlaceAutoFillHelper;
use Controlroom\Field\MapPickerField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PlaceCrudController extends AbstractCrudController
{
    public function __construct(
        private string $googleBackendApiKey,
        private string $googleMapsJsApiKey,
        private PlaceAutoFillHelper $autoFillHelper,
    ) {}

    public static function getEntityFqcn(): string
    {
        return Place::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Place')
            ->setEntityLabelInPlural('Places')
            ->setDefaultSort([
                'country' => 'ASC',
                'name' => 'ASC'
            ])
            ->setFormOptions([
                'attr' => [
                    'data-controller' => 'map-picker',
                ]
            ])
        ;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addJsFile( // added here rather than in MapPickerField.php because of API key
                Asset::new("https://maps.googleapis.com/maps/api/js?key={$this->googleMapsJsApiKey}&libraries=places&callback=initMap")
            )
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        // Tried including everything inside MapPickerField.php, MapPïckerType.php and map_picker_form_theme.html.twig
        // but since mapPicker property must be un-mapped, all elements inside were unmapped as well
        // so instead, we 
        //    - add mapped properties the normal way, with data-map-picker-target
        //    - add MapPickerField containing only unmapped inputs
        //    - add 'data-controller=map-picker" to the form in configureCrud
        //    - add google maps API in configureAssets
        yield MapPickerField::new('mapPicker');

        yield TextField::new('name')->setFormTypeOption('attr', ['data-map-picker-target' => 'name']);
        yield TextField::new('address')->setFormTypeOption('attr', ['data-map-picker-target' => 'address']);
        yield TextField::new('googlePlaceId')->setFormTypeOption('attr', ['data-map-picker-target' => 'placeId']);

        yield NumberField::new('latitude')
            ->setNumDecimals(4)
            ->setFormTypeOption('scale', 7)
            ->setFormTypeOption('attr', ['data-map-picker-target' => 'lat'])
        ;

        yield NumberField::new('longitude')
            ->setNumDecimals(4)
            ->setFormTypeOption('scale', 7)
            ->setFormTypeOption('attr', ['data-map-picker-target' => 'lng'])
        ;

        yield AssociationField::new('placeTags')
            ->setFormTypeOption('by_reference', false) // important for ManyToMany when using add/remove methods
            ->setTemplatePath('@controlroom/field/tags.html.twig')
        ;

        yield AssociationField::new('country')
                ->setFormTypeOption('help', 'Leave blank for autofill from place tag')
        ;
                    
        yield TextareaField::new('descriptionEn', 'Description EN');
        yield TextareaField::new('descriptionFr', 'Description FR');
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

    private function _customFormProcessing(Place $place): void
    {
        $this->autoFillHelper->autoAssignCountry($place);
    }
}