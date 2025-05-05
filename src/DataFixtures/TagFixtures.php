<?php

namespace App\DataFixtures;

use App\Entity\Tag\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    private const array TAGS = [
        [
            'nameFr' => 'Bivouac',
            'nameEn' => 'Hiking/trek camp',
        ],
        [
            'nameFr' => 'Rando',
            'nameEn' => 'Hiking/trek',
        ],
        [
            'nameFr' => 'Rando en autonomie',
            'nameEn' => 'Self-supported hike/trek',
        ],
        [
            'nameFr' => 'Lieu de travail',
            'nameEn' => 'Working place',
        ],
        [
            'nameFr' => 'Bibli',
            'nameEn' => 'Libray',
        ],
        [
            'nameFr' => 'Belles rencontres',
            'nameEn' => 'Memorable strangers',
        ],
        [
            'nameFr' => 'Enfants',
            'nameEn' => 'Kids',
        ],
        [
            'nameFr' => 'Solo trip',
            'nameEn' => 'Solo trip',
        ],
        [
            'nameFr' => 'Family',
            'nameEn' => 'Famille',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::TAGS as $item) {
            
            $tag = new Tag();

            $tag->setNameFr($item['nameFr']);
            $tag->setNameEn($item['nameEn']);

            $this->addReference('tag-' . $item['nameEn'], $tag);
        
            $manager->persist($tag);
        }

        $manager->flush();
    }
}
