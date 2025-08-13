<?php

namespace App\Entity\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait HasPeriodTrait
{
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $endedAt = null;


    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeImmutable $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getEndedAt(): ?\DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function setEndedAt(\DateTimeImmutable $endedAt): static
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    public function getPeriod(): string
    {
        if ($this->startedAt->format('m-Y') == $this->endedAt->format('m-Y')) {
            return $this->startedAt->format('M Y');
        }

        // Use En dash (–) for range
        // no space between simple ranges like Jan–Feb
        if ($this->startedAt->format('Y') == $this->endedAt->format('Y')) {
            return $this->startedAt->format('M') . '–' . $this->endedAt->format('M Y');
        }

        // Use En dash (–) for range
        // Add space between complex dates like Dec 2023 – Jan 2024
        return $this->startedAt->format('M Y') . ' – ' . $this->endedAt->format('M Y');
    }
}