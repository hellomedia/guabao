<?php

namespace App\Pack\Media\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait ImageTrait
{
    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\Column(length: 255)]
    private ?string $originalFilename = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    /**
     * image path relative to uploads dir
     */
    #[ORM\Column(length: 255)]
    private ?string $path = null;

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
}