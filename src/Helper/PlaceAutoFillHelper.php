<?php 

namespace App\Helper;

use App\Entity\Place;

class PlaceAutoFillHelper
{
    public function __construct(
    )
    { 
    }

    public function autoAssignCountry(Place $place): void
    {
        if ($place->getCountry() !== null) {
            return; // already set
        }

        if ($place->getPlaceTags()->isEmpty()) {
            return;
        }

        $placeTag = $place->getPlaceTags()->first();
        $country = $placeTag->getCountry();

        if ($country == null) {
            return;
        }

        $place->setCountry($country);
    }
}