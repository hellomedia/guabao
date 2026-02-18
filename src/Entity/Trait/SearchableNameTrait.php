<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

/**
 * Allows to search for crêpe by typing "crepe"
 * Or Gỏi cuốn by typing "goi cuon".
 * 
 */
trait SearchableNameTrait
{
    /**
     * Normalized name used for accent-insensitive search.
     * 
     * IMPORTANT:
     *      
     *      For performance, add this index on the table mapping:
     * 
     *      #[ORM\Index(name: 'name_search_idx', columns: ['name_search'])]
     * 
     */
    #[ORM\Column(length: 150, nullable: true)]
    private ?string $nameSearch = null;

    public function getNameSearch(): ?string
    {
        return $this->nameSearch;
    }

    public function setNameSearch(string $nameSearch): static
    {
        $this->nameSearch = $nameSearch;

        return $this;
    }
}
