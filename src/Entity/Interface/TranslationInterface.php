<?php

namespace App\Entity\Interface;

/**
 * NB: We avoid methods with TranslableInterface in the signature.
 * If we implement the methods with the correct translable class (ie: Listing)
 * so we can get IDE autocompletion, php treats it as a non-matching signature
 * -- eventhough it implements TranslableInterface
 */
interface TranslationInterface
{
    public function getLocale(): string;

    public function setLocale(string $locale);

    // public function getTranslatable(): TranslatableInterface;

    // public function setTranslatable(TranslatableInterface $translatable);
}
