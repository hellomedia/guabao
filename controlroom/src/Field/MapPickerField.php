<?php

namespace Controlroom\Field;

use Controlroom\Form\Type\MapPickerType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

final class MapPickerField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(MapPickerType::class)
            ->addFormTheme('@controlroom/field/map_picker_form_theme.html.twig')
            ->onlyOnForms()
        ;
    }
}
