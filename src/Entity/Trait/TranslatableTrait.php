<?php

namespace App\Entity\Trait;

trait TranslatableTrait
{
    // Add this property in your entity

    // #[ORM\OneToMany(targetEntity: 'xxxxxTranslation', mappedBy: 'translatable', indexBy: 'locale', cascade: ['persist', 'remove'])]
    // private Collection $translations;

    // Add these in your entity with specific return type

    // public function getCurrentLocaleTranslation(): ?TranslationInterface
    // {
    //     if (false == $this->hasCurrentLocaleTranslation()) {
    //         // no translation for current locale
    //         // ==> use default translation
    //         return false;
    //     }

    //     return $this->getTranslations()->first();
    // }

    // public function getTranslation(string $locale): ?TranslationInterface
    // {
    //     return $this->getTranslations()->get($locale);
    // }

    // public function addTranslation(TranslationInterface $translation): static
    // {
    //     if (!$this->translations->contains($translation)) {
    //         $this->translations->add($translation);
    //         $translation->setTranslatable($this);
    //     }

    //     return $this;
    // }


    public function hasCurrentLocaleTranslation(): bool
    {
        if ($this->getTranslations()->count() > 1) {
            throw new \Exception('More than 1 translations in translations array. Call to getCurrentTranslation() not reliable.');
        }

        if ($this->getTranslations()->count() == 0) {
            // no translation for current locale
            return false;
        }

        return true;
    }

    public function hasTranslation(string $locale): bool
    {
        return $this->getTranslations()->containsKey($locale);
    }
}
