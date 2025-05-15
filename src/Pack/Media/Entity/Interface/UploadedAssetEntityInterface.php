<?php

namespace App\Pack\Media\Entity\Interface;

interface UploadedAssetEntityInterface 
{
    public function getPath(): ?string;

    public function setToken(string $token);

    public function setOriginalFilename(string $originalFilename);

    public function setFilename(string $filename);

    public function setPath(string $imageUploadsDir);

    public function getSubDirs(): ?string;
}