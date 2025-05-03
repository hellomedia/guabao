<?php

namespace App\Entity\Interface;

/**
 * Marker interface
 * 
 * Indicates that the entity has i18n name in DB
 */
interface LocalizedNameInterface
{
    public function getNameFr(): ?string;

    public function getNameEn(): ?string;

    public function getName(string $locale): ?string;
}