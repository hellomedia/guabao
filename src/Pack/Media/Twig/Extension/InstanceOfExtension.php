<?php

namespace App\Pack\Media\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

class InstanceOfExtension extends AbstractExtension
{
    public function getTests(): array
    {
        return [
            new TwigTest('instanceof', [$this, 'isInstanceOf']),
        ];
    }

    public function isInstanceOf($object, string $class): bool
    {
        return $object instanceof $class;
    }
}
