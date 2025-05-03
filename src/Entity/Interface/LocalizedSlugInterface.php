<?php

namespace App\Entity\Interface;

/**
 * Marker interface
 * 
 * Indicates that the entity has i18n slugs
 */
interface LocalizedSlugInterface
{
    public function getSlugFr(): ?string;

    public function getSlugEn(): ?string;

    public function getSlug(string $locale): ?string;
}