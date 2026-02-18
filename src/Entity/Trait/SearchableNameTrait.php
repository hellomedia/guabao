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
     * Note: length: 300 -- unicode normalization can take much more characters
     * than the original string (chinese characters)
     * 
     * IMPORTANT:
     *      
     *      For performance, add this index on the table mapping:
     * 
     *      #[ORM\Index(name: 'name_search_idx', columns: ['name_search'])]
     * 
     */
    #[ORM\Column(length: 300, nullable: true)]
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
