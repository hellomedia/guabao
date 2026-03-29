<?php

namespace App\Entity\Stats;

use App\Entity\Interface\EntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * An anonymous visitor is really a browser
 * Anonymous users are not logged in, so we can only track a browser.
 */
#[ORM\Entity]
#[ORM\Index(columns: ['first_seen_at'], name: 'idx_anonymous_visitor_first_seen_at')]
#[ORM\Index(columns: ['last_seen_at'], name: 'idx_anonymous_visitor_last_seen_at')]
class AnonymousVisitor implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private string $visitorId;

    #[ORM\Column]
    private \DateTimeImmutable $firstSeenAt;

    #[ORM\Column]
    private \DateTimeImmutable $lastSeenAt;

    #[ORM\Column]
    private int $pageCount = 0;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $userAgent = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $alias = null;

    /**
     * @var Collection<int, AnonymousVisit>
     */
    #[ORM\OneToMany(targetEntity: AnonymousVisit::class, mappedBy: 'visitor')]
    private Collection $visits;

    public function __construct()
    {
        $this->visits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->alias ?? ('visitor' . $this->id );
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

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): static
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): static
    {
        $this->alias = $alias;

        return $this;
    }

    public function getFirstSeenAt(): ?\DateTimeImmutable
    {
        return $this->firstSeenAt;
    }

    public function setFirstSeenAt(\DateTimeImmutable $firstSeenAt): static
    {
        $this->firstSeenAt = $firstSeenAt;

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

    public function getVisitCount(): ?int
    {
        return $this->visits->count();
    }

    public function incrementPageCount(): void
    {
        $this->pageCount++;
    }

    /**
     * @return Collection<int, AnonymousVisit>
     */
    public function getVisits(): Collection
    {
        return $this->visits;
    }

    public function addVisit(AnonymousVisit $visit): static
    {
        if (!$this->visits->contains($visit)) {
            $this->visits->add($visit);
            $visit->setVisitor($this);
        }

        return $this;
    }

    public function removeVisit(AnonymousVisit $visit): static
    {
        if ($this->visits->removeElement($visit)) {
            // set the owning side to null (unless already changed)
            if ($visit->getVisitor() === $this) {
                $visit->setVisitor(null);
            }
        }

        return $this;
    }

    public function getCountryCodes(): array
    {
        $countries = $this->visits
            ->map(fn(AnonymousVisit $visit) => $visit->getCountryCode())
            ->filter(fn(?string $country) => $country !== null)
            ->toArray();

        return array_values(array_unique($countries));
    }

    public function getCountryNames(): array
    {
        $names = [];

        foreach ($this->getCountryCodes() as $code) {
            $name = \Locale::getDisplayRegion('-' . $code, 'en');

            $names[] = $name ?: $code;
        }

        sort($names);

        return $names;
    }

    public function getCountryNamesAsString(): string
    {
        return implode(', ', $this->getCountryNames());
    }

    public function getCities(): array
    {
        $cities = $this->visits
            ->map(fn(AnonymousVisit $visit) => $visit->getCityName())
            ->filter(fn(?string $city) => $city !== null)
            ->toArray();

        return array_values(array_unique($cities));
    }

    public function getCitiesAsString(): string
    {
        return implode(', ', $this->getCities());
    }

}