<?php

namespace App\Entity\Stats;

use App\Entity\Interface\EntityInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Index(columns: ['visitor_id'], name: 'idx_anonymous_visit_visitor_id')]
#[ORM\Index(columns: ['started_at'], name: 'idx_anonymous_visit_started_at')]
class AnonymousVisit implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128, unique: true)]
    private string $sessionId;

    #[ORM\Column(length: 64)]
    private string $visitorId;

    #[ORM\Column]
    private \DateTimeImmutable $startedAt;

    #[ORM\Column]
    private \DateTimeImmutable $lastSeenAt;

    #[ORM\Column]
    private int $pageCount = 0;

    #[ORM\Column]
    private bool $isReturning = false;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $countryCode = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $cityName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstPath = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $landingReferrer = null;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $userAgent = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        return 'visit ' . $this->id . ' from ' . $this->visitorId;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): static
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getVisitorId(): ?string
    {
        return $this->visitorId;
    }

    public function setVisitorId(string $visitorId): static
    {
        $this->visitorId = $visitorId;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeImmutable $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getLastSeenAt(): ?\DateTimeImmutable
    {
        return $this->lastSeenAt;
    }

    public function setLastSeenAt(\DateTimeImmutable $lastSeenAt): static
    {
        $this->lastSeenAt = $lastSeenAt;

        return $this;
    }

    public function getPageCount(): ?int
    {
        return $this->pageCount;
    }

    public function setPageCount(int $pageCount): static
    {
        $this->pageCount = $pageCount;

        return $this;
    }

    public function isReturning(): ?bool
    {
        return $this->isReturning;
    }

    public function setIsReturning(bool $isReturning): static
    {
        $this->isReturning = $isReturning;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): static
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getCityName(): ?string
    {
        return $this->cityName;
    }

    public function setCityName(?string $cityName): static
    {
        $this->cityName = $cityName;

        return $this;
    }

    public function getFirstPath(): ?string
    {
        return $this->firstPath;
    }

    public function setFirstPath(?string $firstPath): static
    {
        $this->firstPath = $firstPath;

        return $this;
    }

    public function getLandingReferrer(): ?string
    {
        return $this->landingReferrer;
    }

    public function setLandingReferrer(?string $landingReferrer): static
    {
        $this->landingReferrer = $landingReferrer;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): static
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function incrementPageCount(): void
    {
        $this->pageCount++;
    }
}