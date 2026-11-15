<?php

namespace App\Enum;

use App\Entity\Trait\KeyTrait;

/**
 * TripTag::$key values the code relies on (trip pages by tag).
 * Kept out of TripTagFixtures: the fixtures bundle is not installed in prod.
 *
 * @see KeyTrait for what a key is: a stable identifier, unlike the translated names and slugs
 */
enum TripTagKey: string
{
    case SLOW_TRAVEL = 'slowtravel';
    case HIKING = 'hiking';
    case SOLO = 'solo';
    case WITH_FRIENDS = 'friends';
}
