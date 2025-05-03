<?php

namespace App\Entity\Trait;

use App\Entity\Interface\TranslatableInterface;
use Doctrine\ORM\Mapping as ORM;

trait TranslationTrait
{
    // Add this to your entity
    // #[ORM\ManyToOne(inversedBy: 'translations')]
    // #[ORM\JoinColumn(name: 'translatable_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    // protected ?TranslatableEntity $translatable = null;

    // Add these methods with specific signatures
    
    // public function getTranslatable(): TranslatableInterface
    // {
    //     return $this->translatable;
    // }

    // public function setTranslatable(TranslatableInterface $translatable)
    // {
    //     $this->translatable = $translatable;
    // }

    #[ORM\Column(length: 10)]
    protected $locale = null;

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale)
    {
        $this->locale = $locale;
    }


    public function isNotYetAttached(): bool
    {
        return $this->translatable == null;
    }
}
