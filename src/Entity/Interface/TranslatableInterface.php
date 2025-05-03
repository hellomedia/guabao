<?php

namespace App\Entity\Interface;

use Doctrine\Common\Collections\Collection;

/**
 * NB: We avoid methods with TranslationInterface in the signature.
 * If we implement the methods with the correct translation class (ie: ListingTranslation)
 * so we can get IDE autocompletion, php treats it as a non-matching signature
 * -- eventhough it implements TranslationInterface
 */
interface TranslatableInterface
{
    public function getTranslations(): Collection;

    public function hasCurrentLocaleTranslation(): bool;

    public function hasTranslation(string $locale): bool;

    // public function getCurrentLocaleTranslation(): ?TranslationInterface

    // public function getTranslation(string $locale): ?TranslationInterface

    // public function addTranslation(TranslationInterface $translation): static
}
