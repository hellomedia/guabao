<?php

namespace App\Pack\Media\Entity\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait ImageTrait
{
    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\Column(length: 255)]
    private ?string $originalFilename = null;

    /**
     * Unique token to guarantee filename uniqueness
     * usage: $token = uniqid();
     * Also used to compute subdirs
     */
    #[ORM\Column(length: 255)]
    private ?string $token = null;

    /**
     * image path relative to uploads dir
     */
    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $exifData = null;

    /** 
     * Dimensions before resizing at upload
     * (useful for Kotcop)
     */
    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $originalHeight = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $originalWidth = null;

    /** 
     * Dimensions after resizing at upload
     * (useful for width/height image attributes)
     */
    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $height = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $width = null;

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(string $originalFilename): static
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    /**
     * get path relative to uploads dir
     * including the filename
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * set path relative to uploads dir
     */
    public function setPath(string $imageUploadsDir): static
    {
        $this->path = $imageUploadsDir . '/' . $this->getSubDirs() . '/' . $this->filename;

        return $this;
    }

    public function getSubDirs(): ?string
    {
        return substr($this->getToken(), 0, 2) . '/' . substr($this->getToken(), 2, 2);
    }

    public function getExifData(): ?array
    {
        return $this->exifData;
    }

    public function setExifData(array $exifData): self
    {
        $this->exifData = $exifData;

        return $this;
    }

    public function getOriginalHeight(): ?int
    {
        return $this->originalHeight;
    }

    public function setOriginalHeight(?int $originalHeight): static
    {
        $this->originalHeight = $originalHeight;

        return $this;
    }

    public function getOriginalWidth(): ?int
    {
        return $this->originalWidth;
    }

    public function setOriginalWidth(?int $originalWidth): static
    {
        $this->originalWidth = $originalWidth;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int|float $height): static
    {
        $this->height = (int) round($height); // round returns a float

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int|float $width): static
    {
        $this->width = (int) round($width); // round returns a float

        return $this;
    }

    public function getAspectRatio(): ?float
    {
        return $this->width / $this->height;
    }
}