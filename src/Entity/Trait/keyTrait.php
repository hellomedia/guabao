<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait KeyTrait
{
    /**
     * Stores a descriptive and stable (unlike translations) identifier.
     * Useful to build translation keys for related things
     * such as meta keywords, meta description, datafixture id, etc.
     * when those meta translations are handled in translation files.
     * Not as useful when those meta translations are handled in database.
     * Should not be changed.
     */
    #[ORM\Column(length: 100, unique: true)]
    private ?string $key = null;

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }
}
