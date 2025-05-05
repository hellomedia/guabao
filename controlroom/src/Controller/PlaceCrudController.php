<?php

namespace Controlroom\Controller;

use App\Entity\Place;
use App\Entity\Tag\PlaceTag;
use Controlroom\Field\MapPickerField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PlaceCrudController extends AbstractCrudController
{
    public function __construct(
        private string $googleBackendApiKey,
        private string $googleMapsJsApiKey,
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

        yield NumberField::new('latitude')->setFormTypeOption('attr', ['data-map-picker-target' => 'lat']);
        yield NumberField::new('longitude')->setFormTypeOption('attr', ['data-map-picker-target' => 'lng']);

        yield AssociationField::new('placeTags')
            ->setFormTypeOptions([
                'by_reference' => false, // important for ManyToMany when using add/remove methods
                'choice_label' => function (PlaceTag $placeTag) {
                    $locale = $this->getContext()?->getRequest()?->getLocale() ?? 'fr';
                    return $placeTag->getName($locale);
                }
            ])
            ->setTemplatePath('@controlroom/field/tags.html.twig')
            ->setHelp('Hold Ctrl (or Cmd) to select multiple tags');

        yield TextField::new('country')
            ->hideOnForm();

        yield TextField::new('descriptionFr', 'Description FR');
        yield TextField::new('descriptionEn', 'Description EN');
    }
}