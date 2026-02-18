<?php

namespace App\Entity\Interface;

/**
 * Marker interface
 * 
 * Indicates that the entity has a searchableName property
 */
interface SearchableNameInterface
{
    public function getNameSearch(): ?string;

    public function setNameSearch(string $nameSearch);
}