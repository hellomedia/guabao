<?php

namespace App\Entity\Interface;

/**
 * Marker interface
 * 
 * Indicates that the entity has a originalName property
 */
interface OriginalNameInterface
{
    public function getOriginalName(): ?string;

    public function setOriginalName(?string $originalName);
}