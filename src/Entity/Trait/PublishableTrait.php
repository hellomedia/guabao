<?php

namespace App\Entity\Trait;

use App\Entity\ListingPublication;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

trait PublishableTrait
{
    // workflow component: 
    // you don't need to set the initial marking in the constructor or any other method;
    // this is configured in the workflow with the 'initial_marking' option
    #[ORM\Column(length: 1)]
    private string $state;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $lastPublishedAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $lastUnpublishedAt = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(onDelete: 'set null')] // for Fixtures when truncating DB
    private ?ListingPublication $latestPublication = null;

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * for easyadmin
     * https://symfony.com/bundles/EasyAdminBundle/current/fields/ChoiceField.html#setchoices
     */
    public function getStateChoices(): array
    {
        return [
            'UNPUBLISHED' => self::STATE_UNPUBLISHED,
            'PUBLISHED' => self::STATE_PUBLISHED,
            'ARCHIVED' => self::STATE_ARCHIVED,
            'DELETED' => self::STATE_DELETED,
        ];
    }

    public function setLastPublishedAt(DateTimeImmutable $lastPublishedAt): void
    {
        $this->lastPublishedAt = $lastPublishedAt;
    }

    public function getLastPublishedAt(): ?DateTimeImmutable
    {
        return $this->lastPublishedAt;
    }

    public function setLastUnpublishedAt(DateTimeImmutable $lastUnpublishedAt): void
    {
        $this->lastUnpublishedAt = $lastUnpublishedAt;
    }

    public function getLastUnpublishedAt(): ?DateTimeImmutable
    {
        return $this->lastUnpublishedAt;
    }

    public function setLatestPublication(ListingPublication $latestPublication): static
    {
        $this->latestPublication = $latestPublication;

        return $this;
    }

    public function getLatestPublication(): ?ListingPublication
    {
        return $this->latestPublication;
    }
}
