<?php 

namespace App\Entity\Stats;

use App\Entity\Interface\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class AnonymousPageView implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: AnonymousVisit::class, inversedBy: 'pageViews')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private AnonymousVisit $visit;

    #[ORM\Column]
    private \DateTimeImmutable $visitedAt;

    #[ORM\Column(length: 255)]
    private string $path;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $routeName = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $queryString = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $referrer = null;

    public function __toString()
    {
        return 'page view ' . $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVisitedAt(): ?\DateTimeImmutable
    {
        return $this->visitedAt;
    }

    public function setVisitedAt(\DateTimeImmutable $visitedAt): static
    {
        $this->visitedAt = $visitedAt;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function setRouteName(?string $routeName): static
    {
        $this->routeName = $routeName;

        return $this;
    }

    public function getQueryString(): ?string
    {
        return $this->queryString;
    }

    public function setQueryString(?string $queryString): static
    {
        $this->queryString = $queryString;

        return $this;
    }

    public function getReferrer(): ?string
    {
        return $this->referrer;
    }

    public function setReferrer(?string $referrer): static
    {
        $this->referrer = $referrer;

        return $this;
    }

    public function getVisit(): ?AnonymousVisit
    {
        return $this->visit;
    }

    public function setVisit(?AnonymousVisit $visit): static
    {
        $this->visit = $visit;

        return $this;
    }
}